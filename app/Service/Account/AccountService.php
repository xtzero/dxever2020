<?php


namespace App\Service\Account;


use App\Models\SysMenuModel;
use App\Models\SysRoleMenuModel;
use App\Models\SysRoleModel;
use App\Models\SysRoleUserModel;
use App\Models\SysUserModel;
use App\Service\Base\BaseService;
use App\Support\ArraySupport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class AccountService extends BaseService
{
    public function login($params)
    {
        $user = (new SysUserModel())->baseSelectFirst([
            'username' => $params['username'],
            'is_del' => 0
        ]);
        if (!$user) {
            return self::ajaxError('该用户不存在');
        }
        $passMd5 =  md5($params['password']) . '-' . md5($user['uid'] . config('keys.passwordSalt'));
        if ($user['password'] != $passMd5) {
            return self::ajaxError('密码不正确');
        }
        $token = md5(implode('|', [
            $user['uid'],
            time()
        ]));
        Redis::setex(config('keys.redisKey.tokenPrefix') . $token, 86400, serialize($user));

        $roleUsers = (new SysRoleUserModel())->baseSelect([
            'uid' => $user['uid']
        ], ['role_id']);
        if ($roleUsers) {
            $roles = ArraySupport::stdClassToArray((new SysRoleModel())->baseQuery([
                'is_del' => 0
            ], ['id', 'name'])->whereIn('id', array_column($roleUsers, 'role_id'))->get());
            $sysRoleMenus = ArraySupport::stdClassToArray((new SysRoleMenuModel())
                ->baseQuery([], ['menu_id'])
                ->whereIn('role_id', array_column($roles, 'id'))
                ->get());
            $menus = ArraySupport::stdClassToArray((new SysMenuModel())->baseQuery([
                'is_del' => 0
            ], ['id', 'path', 'type', 'order'])->whereIn('id', array_column($sysRoleMenus, 'menu_id'))->get());
        } else {
            $roles = [];
            $menus = [];
        }
        return self::ajax([
            'token' => $token,
            'user' => [
                'uid' => $user['uid'],
                'name' => $user['name']
            ],
            'roles' => $roles,
            'menus' => $menus
        ]);
    }
}
