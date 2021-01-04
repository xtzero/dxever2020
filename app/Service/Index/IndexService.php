<?php
namespace App\Service\Index;

use App\Models\BunetModel;
use App\Models\BusinessModel;
use App\Models\PayorderModel;
use App\Models\UserModel;
use App\Service\Base\BaseService;
use App\Support\ArraySupport;
use Illuminate\Support\Facades\DB;

class IndexService extends BaseService
{
    public function ifBusinessExist($bid)
    {
        $ifBusinessExist = (new BusinessModel())->baseSelectCount([
            'bid' => $bid,
            'valid' => 1
        ]) > 0 ? 1 : 0;
        return self::ajax([
            'ifBusinessExist' => $ifBusinessExist
        ]);
    }

    public function baseInfo($bid)
    {
        $business = (new BusinessModel())->baseSelectFirst([
            'bid' => $bid,
            'valid' => 1
        ]);
        if (!$business) {
            return self::ajaxError('该商家不存在');
        }

        $business['pic'] = explode('|', $business['pic']);
        if ($business['ownid']) {
            $owner = (new UserModel())->baseSelectFirst([
                'uid' => $business['ownid']
            ],['uid', 'openid', 'nickname', 'headimg', 'vip']);
            $business['owner'] = $owner ?? [];
        }

        if ($business['jobid']) {
            $jober = (new UserModel())->baseSelectFirst([
                'uid' => $business['jobid']
            ],['uid', 'openid', 'nickname', 'headimg', 'vip']);
            $business['jober'] = $jober ?? [];
        }

        return self::ajax($business);
    }

    public function userList($params, $bid)
    {
        $whereArr = [
            ['bid', '=', $bid]
        ];
        $count = (new BunetModel())->baseSelectCount($whereArr);
        if ($count <= 0) {
            return self::ajax([]);
        } else {
            $users = ArraySupport::stdClassToArray((new BunetModel())->baseSelectByPage(
                $whereArr,
                ['*'],
                $params['limit'] ?: 20,
                $params['page'] ?: 1
            )->get());

            $uidToUser = (new UserModel())->baseCreateDictionary(
                array_column($users, 'openid'),
                'openid', 'openid',
                ['openid', 'nickname', 'headimg']
            );
            foreach ($users as &$v) {
                $_user = $uidToUser[$v['openid']];
                $_user['openid'] = substr($_user['openid'], 0, 10);
                $v['user'] =  $_user ?? [];
            }

            return self::ajax([
                'count' => $count,
                'data' => $users
            ]);
        }
    }

    public function orderList($params, $bid)
    {
        $orderQ = DB::table((new PayorderModel())->table);
        $orderQ = $orderQ->where('bid', $bid)->where('paid', 1);
        $userWhere = [];
        if (!empty($params['nickname'])) {
            $userWhere[] = ['nickname', 'like', "%{$params['nickname']}%"];
        }
        if (!empty($params['openid'])) {
            $userWhere[] = ['openid', '=', $params['openid']];
        }

        if (!empty($userWhere)) {
            $users = (new UserModel())->baseSelect($userWhere, ['openid']);
            if (empty($users)) {
                return self::ajax([
                    'count' => 0,
                    'data' => []
                ]);
            } else {
                $orderQ = $orderQ->whereIn('openid', array_column($users, 'openid'));
            }
        }
        if (!empty($params['date_begin'])) {
            $orderQ = $orderQ->where('timestamp', '>=', date('Y-m-d', strtotime($params['date_begin'])) . ' 00:00:00');
        }
        if (!empty($params['date_end'])) {
            $orderQ = $orderQ->where('timestamp', '<=', date('Y-m-d', strtotime($params['date_end'])) . ' 23:59:59');
        }

        $count = $orderQ->select(['*'])->count();
        if ($count <= 0) {
            return self::ajax([
                'count' => 0,
                'data' => []
            ]);
        } else {
            $data = $orderQ
                ->select(['pid', 'openid', 'amount', 'paid', 'timestamp'])
                ->skip(($params['page'] - 1) * $params['limit'])
                ->take($params['limit'])
                ->orderByDesc('timestamp')
                ->get();
            $data = ArraySupport::stdClassToArray($data);

            $openidToUser = (new UserModel())->baseCreateDictionary(
                array_column($data, 'openid'),
                'openid', 'openid', ['openid', 'nickname', 'headimg']
            );
            foreach ($data as &$v) {
                $_user = $openidToUser[$v['openid']] ?? [];
                $_user['openid'] = substr($_user['openid'], 0, 10);
                $v['user'] = $_user;
            }

            return self::ajax([
                'count' => $count,
                'data' => $data
            ]);
        }
    }

    public function dailyStatistic($params, $bid)
    {
        $whereArr = [
            ['bid', '=', $bid]
        ];
        if (!empty($params['date_begin'])) {
            $whereArr[] = ['timestamp', '>=', date('Y-m-d', strtotime($params['date_begin'])) . ' 00:00:00'];
        }
        if (!empty($params['date_end'])) {
            $whereArr[] = ['timestamp', '<=', date('Y-m-d', strtotime($params['date_end'])) . ' 23:59:59'];
        }
        $data = ArraySupport::stdClassToArray(DB::table((new PayorderModel())->table)
            ->select(DB::raw("DATE_FORMAT(timestamp, '%Y-%m-%d') as d, count(pid) as pc, count(distinct openid) as uvc, sum(amount) as s"))
            ->where($whereArr)
            ->groupBy(['d'])
            ->orderBy('d')
            ->get());
        return self::ajax($data);
    }
}
