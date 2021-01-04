<?php


namespace App\Service\Sys;


use App\Models\SysRoleModel;
use App\Models\SysRoleUserModel;
use App\Models\SysUserModel;
use App\Models\UserModel;
use App\Service\Base\BaseService;
use App\Support\ArraySupport;
use App\Support\DateSupport;
use Illuminate\Support\Facades\Log;

class UserService extends BaseService
{
    /**
     * 系统用户列表
     * @param $params
     * @return mixed
     */
    public function sysUserList($params)
    {
        $whereArr = [
            ['is_del', '=', 0]
        ];
        if (!empty($params['username'])) {
            $whereArr[] = ['username', 'like', "%{$params['username']}%"];
        }
        if (!empty($params['name'])) {
            $whereArr[] = ['name', 'like', "%{$params['name']}%"];
        }
        $users = (new SysUserModel())->baseSelect($whereArr, ['uid', 'username', 'name', 'create_time', 'modify_time']);
        $uidToRoles = (new SysRoleUserModel())->baseCreateDictionaryGroup(
            array_column($users, 'uid'),
            'uid', 'uid', ['uid', 'role_id']
        );
        $roleIds = [];
        foreach ($uidToRoles as $v) {
            foreach ($v as $vv) {
                $roleIds[] = $vv['role_id'];
            }
        }
        $roleIdToRole = (new SysRoleModel())->baseCreateDictionary(
            $roleIds,
            'id', 'id', ['id', 'name']
        );
        foreach ($users as &$v) {
            $roles = $uidToRoles[$v['uid']] ?? [];
            if ($roles != []) {
                foreach ($roles as &$vv) {
                    $vv['role_name'] = $roleIdToRole[$vv['role_id']]['name'] ?? '';
                }
            } else {
                $roles = [];
            }
            $v['roles'] =  $roles;
        }
        return self::ajax($users);
    }

    /**
     * 保存系统用户
     * @param $params
     * @param $uid
     * @return false|string
     */
    public function saveSysUser($params, $uid)
    {
        if (!empty($params['uid'])) {
            return $this->editSysUser($params, $uid);
        } else {
            return $this->addSysUser($params, $uid);
        }
    }

    private function addSysUser($params, $uid)
    {
        $saveData = [];
        if (!empty($params['username'])) {
            $usernameExist = (new SysUserModel())->baseSelectFirst([
                'username' => $params['username'],
                'is_del' => 0
            ]);
            if (!empty($usernameExist)) {
                return self::ajaxError('这个用户已存在');
            }
            $saveData['username'] = $params['username'];
        } else {
            return self::ajaxError('需要用户名');
        }

        if (empty($params['password'])) {
            return self::ajaxError('需要密码');
        }

        if (empty($params['name'])) {
            return self::ajaxError('需要名字');
        } else {
            $saveData['name'] = $params['name'];
        }

        try {
            $save = (new SysUserModel())->baseInsert(array_merge($saveData, [
                'is_del' => 0,
                'cretae_user' => $uid,
                'create_time' => DateSupport::now()
            ]));
            $passMd5 = md5($params['password']) . '-' . md5($save . config('keys.passwordSalt'));
            $savePass = (new SysUserModel())->baseUpdate([
                'uid' => $save
            ], [
                'password' => $passMd5
            ]);

            return self::ajax([
                'save' => $save,
                'savePass' => $savePass
            ]);
        } catch (\Exception $e) {
            $msg = "保存系统用户时出现问题，msg:" . $e->getMessage();
            Log::error($msg);
            return self::ajaxError($msg);
        }
    }

    private function editSysUser($params, $uid)
    {
        $saveData = [];
        if (empty($params['uid'])) {
            return self::ajaxError('需要用户id');
        }
        $user = (new SysUserModel())->baseSelectFirst([
            'uid' => $params['uid'],
            'is_del' => 0
        ]);
        if (!$user) {
            return self::ajaxError('该用户不存在');
        }
        if (isset($params['password'])) {
            $passMd5 = md5($params['password']) . '-' . md5($params['uid'] . config('keys.passwordSalt'));
            if ($user['password'] != $passMd5) {
                $saveData['password'] = $passMd5;
            }
        }
        if (isset($params['name']) && $user['name'] != $params['name']) {
            $saveData['name'] = $params['name'];
        }
        if (isset($params['username']) && $user['username'] != $params['username']) {
            $saveData['username'] = $params['username'];
        }

        if (!empty($saveData)) {
            $saveData = array_merge($saveData, [
                'modify_user' => $uid,
                'modify_time' => DateSupport::now()
            ]);
        } else {
            return self::ajaxError('没有要修改的数据');
        }
        try {
            $save = (new SysUserModel())->baseUpdate([
                'uid' => $params['uid']
            ], $saveData);

            return self::ajax([
                'save' => $save
            ]);
        } catch (\Exception $e) {
            $msg = "保存系统用户时出现问题，msg:" . $e->getMessage();
            Log::error($msg);
            return self::ajaxError($msg);
        }
    }

    public function deleteSysUser($params, $uid)
    {
        if (empty($params['uid'])) {
            return self::ajaxError('需要用户id');
        }
        $user = (new SysUserModel())->baseSelectFirst([
            'uid' => $params['uid'],
            'is_del' => 0
        ]);
        if (!$user) {
            return self::ajaxError('该用户不存在');
        }

        try {
            $update = (new SysUserModel())->baseUpdate([
                'uid' => $params['uid']
            ], [
                'is_del' => 1,
                'modify_user' => $uid,
                'modify_time' => DateSupport::now()
            ]);

            return self::ajax([
                'update' => $update
            ]);
        } catch (\Exception $e) {
            $msg = "保存系统用户时出现问题，msg:" . $e->getMessage();
            Log::error($msg);
            return self::ajaxError($msg);
        }
    }

    public function setUserRole($params, $uid)
    {
        if (empty($params['uid'])) {
            return self::ajaxError('需要指定用户');
        }
        try {
            (new SysRoleUserModel())->baseDelete([
                'uid' => $params['uid']
            ]);

            if (!empty($params['role_ids'])) {
                $roleIds = explode(',', $params['role_ids']);
                foreach ($roleIds as $v) {
                    (new SysRoleUserModel())->baseInsert([
                        'uid' => $params['uid'],
                        'role_id' => $v,
                        'create_time' => DateSupport::now()
                    ]);
                }
                return self::ajax([
                    'res' => 1
                ]);
            } else {
                return self::ajax([
                    'res' => 0
                ]);
            }
        } catch (\Exception $e) {
            $msg = "保存用户角色时出现问题，msg:" . $e->getMessage();
            Log::error($msg);
            return self::ajaxError($msg);
        }
    }
}
