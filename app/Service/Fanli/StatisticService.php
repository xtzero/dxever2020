<?php


namespace App\Service\Fanli;


use App\Models\BusinessModel;
use App\Models\PayorderModel;
use App\Models\UserModel;
use App\Service\Base\BaseService;
use App\Support\ArraySupport;
use Illuminate\Support\Facades\DB;

class StatisticService extends BaseService
{
    public function userDailyStatistic($params)
    {
        $where = [];
        if (!empty($params['date_begin'])) {
            $where[] = ['timestamp', '>=', date('Y-m-d', strtotime($params['date_begin']))];
        }
        if (!empty($params['date_end'])) {
            $where[] = ['timestamp', '<=', date('Y-m-d', strtotime($params['date_end']))];
        }
        $order = [];
        if (!empty($params['order'])) {
            $order['order'] = $params['order'];
        }
        if (!empty($params['order_method'])) {
            $order['order_method'] = $params['order_method'];
        }

        $data = (new UserModel())->getUserDailyStatistic($where, $order);
        return self::ajax($data);
    }

    public function userOrderRank($params)
    {
        $whereArr = [];
        if (!empty($params['date_begin'])) {
            $whereArr[] = ['timestamp', '>=', $params['date_begin']];
        }
        if (!empty($params['date_end'])) {
            $whereArr[] = ['timestamp', '<=', $params['date_end']];
        }

        $count = (new PayorderModel())->baseSelectCount($whereArr);
        if ($count <= 0) {
            return self::ajax([
                'count' => $count,
                'data' => []
            ]);
        } else {
            $data = ArraySupport::stdClassToArray((new PayorderModel())->baseSelectByPage(
                $whereArr,
                DB::raw('openid, sum(amount) as s, max(timestamp) as t'),
                $params['limit'] ?? 20,
                $params['page'] ?? 1
            )->groupBy(['openid'])->orderByDesc('s')->get());

            $uidToUser = (new UserModel())->baseCreateDictionary(
                array_column($data, 'openid'),
                'openid', 'openid', ['openid', 'uid', 'nickname', 'headimg']
            );

            foreach ($data as &$v) {
                $v['user'] = $uidToUser[$v['openid']];
            }

            return [
                'count' => $count,
                'data' => $data
            ];
        }
    }

    public function businessOrderRank($params)
    {
        $whereArr = [
            ['bid', '>', '0']
        ];
        if (!empty($params['date_begin'])) {
            $whereArr[] = ['timestamp', '>=', $params['date_begin']];
        }
        if (!empty($params['date_end'])) {
            $whereArr[] = ['timestamp', '<=', $params['date_end']];
        }

        $count = (new PayorderModel())->baseSelectCount($whereArr);
        if ($count <= 0) {
            return self::ajax([
                'count' => $count,
                'data' => []
            ]);
        } else {
            $data = ArraySupport::stdClassToArray((new PayorderModel())->baseSelectByPage(
                $whereArr,
                DB::raw('bid, sum(amount) as s, max(timestamp) as t'),
                $params['limit'] ?? 20,
                $params['page'] ?? 1
            )->groupBy(['bid'])->orderByDesc('s')->get());

            $bidToBusiness = (new BusinessModel())->baseCreateDictionary(
                array_column($data, 'bid'),
                'bid', 'bid',
                ['bid', 'onediscount', 'flimit', 'twodiscount', 'threediscount', 'name', 'location', 'region']
            );

            foreach ($data as &$v) {
                $v['business'] = $bidToBusiness[$v['bid']];
            }

            return [
                'count' => $count,
                'data' => $data
            ];
        }
    }

    public function orderDailySummary($params)
    {
        $whereArr = [
            ['paid', '=', 1],
            ['bid', '<>', 0],
            ['bid', '<>', -1],
        ];
        if (!empty($params['date_begin'])) {
            $whereArr[] = ["DATE_FORMAT(timestamp, '%Y-%m-%d')", '>=', $params['date_begin']];
        }
        if (!empty($params['date_end'])) {
            $whereArr[] = ["DATE_FORMAT(timestamp, '%Y-%m-%d')", '<=', $params['date_end']];
        }
        $dbRes = DB::connection('mysql_dxever')->table((new PayorderModel())->table)
            ->select(DB::raw("sum(amount) as s, DATE_FORMAT(timestamp, '%Y-%m-%d') as t"))
            ->where($whereArr)
            ->groupBy(['t'])
            ->orderByDesc('t')
            ->get();
        $dbRes = ArraySupport::stdClassToArray($dbRes);

        foreach ($dbRes as &$v) {
            $v['sum'] = $v['s'] / 100;
        }

        return self::ajax($dbRes);
    }
}
