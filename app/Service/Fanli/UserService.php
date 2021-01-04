<?php


namespace App\Service\Fanli;


use App\Models\BusinessModel;
use App\Models\UserModel;
use App\Service\Base\BaseService;
use App\Support\ArraySupport;
use Illuminate\Support\Facades\DB;

class UserService extends BaseService
{
    /**
     * 用户列表
     * @param $params
     * @return false|string
     */
    public function userList($params)
    {
        $whereArr = [];
        if (!empty($params['nickname'])) {
            $whereArr[] = ['nickname', 'like', "%". base64_encode($params['nickname']) ."%"];
        }
        if (!empty($params['openid'])) {
            $whereArr[] = ['openid', '=', $params['openid']];
        }

        $count = (new UserModel())->baseSelectCount($whereArr);
        if ($count <= 0) {
            return self::ajax([
                'count' => $count,
                'data' => []
            ]);
        } else {
            $data = ArraySupport::stdClassToArray((new UserModel())->baseSelectByPage(
                $whereArr,
                ['uid', 'openid', 'balance', 'income', 'oneagent', 'twoagent', 'vip', 'nickname', 'headimg', 'timestamp'],
                $params['limit'] ?? 20,
                $params['page'] ?? 1
            )->orderByDesc('timestamp')->get());

            $uidToUser = (new UserModel())->baseCreateDictionary(
                array_merge(
                    array_column($data, 'oneagent'),
                    array_column($data, 'twoagent')
                ), 'uid', 'uid',
                ['uid', 'openid', 'nickname', 'headimg']
            );

            foreach ($data as $k => &$v) {
                $v['oneagent_user'] = $uidToUser[$v['oneagent']];
                $v['twoagent_user'] = $uidToUser[$v['twoagent']];
            }

            return self::ajax([
                'count' => $count,
                'data' => $data
            ]);
        }
    }

    public function businessList($params)
    {
        $whereArr = [
            ['valid', '=', 1]
        ];

        if (!empty($params['name'])) {
            $whereArr[] = [
                'name', 'like', "%{$params['name']}%"
            ];
        }
        $count = (new BusinessModel())->baseSelectCount($whereArr);
        if ($count <= 0) {
            return self::ajax([
                'count' => $count,
                'data' => []
            ]);
        } else {
            $data = ArraySupport::stdClassToArray((new BusinessModel())->baseSelectByPage(
                $whereArr,
                ['bid', 'location', 'region', 'ownid', 'jobid', 'timestamp'],
                $params['limit'] ?: 20,
                $params['page'] ?: 1
            )->orderByDesc('timestamp')->get());

            $uidToUser = (new UserModel())->baseCreateDictionary(
                array_merge(
                    array_column($data, 'ownid'),
                    array_column($data, 'jobid')
                ), 'uid', 'uid',
                ['uid', 'nickname', 'openid', 'headimg']
            );

            foreach ($data as &$v) {
                $v['owner'] = $uidToUser[$v['ownid']];
                $v['jober'] = $uidToUser[$v['jobid']];
            }

            return self::ajax([
                'count' => $count,
                'data' => $data
            ]);
        }
    }

    /**
     * 商家详情
     * @param $params
     * @return false|string
     */
    public function businessDetail($params)
    {
        if (empty($params['bid'])) {
            return self::ajaxError('需要商家id');
        }
        $business = (new BusinessModel())->baseSelectFirst([
            'bid' => $params['bid'],
            'valid' => 1
        ], ['bid', 'name', 'location', 'region', 'pic', 'ownid', 'jobid']);
        if (!$business) {
            return self::ajaxError('商家不存在');
        }
        $uidToUser = (new UserModel())->baseCreateDictionary(
            [$business['ownid'], $business['jobid']],
            'uid', 'uid',
            ['uid', 'nickname', 'openid', 'headimg']
        );
        $business['owner'] = $uidToUser[$business['ownid']] ?? [];
        $business['jober'] = $uidToUser[$business['jobid']] ?? [];

        return self::ajax($business);
    }
}
