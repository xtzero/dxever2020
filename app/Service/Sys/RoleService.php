<?php


namespace App\Service\Sys;


use App\Models\SysMenuModel;
use App\Models\SysRoleMenuModel;
use App\Models\SysRoleModel;
use App\Service\Base\BaseService;
use App\Support\ArraySupport;
use App\Support\DateSupport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleService extends BaseService
{
    public function roleList($params)
    {
        $whereArr = [
            ['is_del', '=', 0]
        ];
        if (!empty($params['name'])) {
            $whereArr[] = ['name', 'like', "%{$params['name']}%"];
        }
        return self::ajax((new SysRoleModel())->baseSelect($whereArr, ['id', 'name']));
    }

    public function saveRole($params, $uid)
    {
        if (isset($params['id'])) {
            return $this->editRole($params, $uid);
        } else {
            return $this->addRole($params, $uid);
        }
    }

    private function addRole($params, $uid)
    {
        $saveData = [];
        if (!empty($params['name'])) {
            $saveData['name'] = $params['name'];
        } else {
            return self::ajaxError('需要角色名');
        }
        $saveData = array_merge($saveData, [
            'create_user' => $uid,
            'create_time' => DateSupport::now(),
            'is_del' => 0
        ]);
        try {
            $save = (new SysRoleModel())->baseInsert($saveData);

            return self::ajax([
                'save' => $save
            ]);
        } catch (\Exception $e) {
            $msg = "保存角色时出现问题，msg:" . $e->getMessage();
            Log::error($msg);
            return self::ajaxError($msg);
        }
    }

    private function editRole($params, $uid)
    {
        $saveData = [];
        if (empty($params['id'])) {
            return self::ajaxError('需要指定角色id');
        }
        $role = (new SysRoleModel())->baseSelectFirst([
            'id' => $params['id']
        ]);
        if (!$role) {
            return self::ajaxError('这个角色不存在');
        }
        if (isset($params['name']) && $params['name'] != $role['name']) {
            $saveData['name'] = $params['name'];
        }

        if (empty($saveData)) {
            return self::ajaxError('没有要修改的数据');
        } else {
            $saveData = array_merge($saveData, [
                'modify_user' => $uid,
                'modify_time' => DateSupport::now()
            ]);
        }

        try {
            $save = (new SysRoleModel())->baseUpdate([
                'id' => $params['id']
            ], $saveData);

            return self::ajax([
                'save' => $save
            ]);
        } catch (\Exception $e) {
            $msg = "保存角色时出现问题，msg:" . $e->getMessage();
            Log::error($msg);
            return self::ajaxError($msg);
        }
    }

    public function deleteRole($params, $uid)
    {
        if (empty($params['id'])) {
            return self::ajaxError('需要指定角色id');
        }
        try {
            $update = (new SysRoleModel())->baseUpdate([
                'id' => $params['id']
            ], [
                'is_del' => 1,
                'modify_user' => $uid,
                'modify_time' => DateSupport::now()
            ]);

            return self::ajax([
                'update' => $update
            ]);
        } catch (\Exception $e) {
            $msg = "删除角色时出现问题，msg:" . $e->getMessage();
            Log::error($msg);
            return self::ajaxError($msg);
        }
    }

    public function getRoleMenuList($params)
    {
        if (empty($params['id'])) {
            return self::ajaxError('需要菜单id');
        }
        $roleMenus = (new SysRoleMenuModel())->baseSelect([
            'role_id' => $params['id']
        ], ['menu_id']);
        $menuIds = array_column($roleMenus, 'menu_id');

        $menuWhereArr = [
            'is_del' => 0
        ];
        if (!empty($params['type'])) {
            $menuWhereArr['type'] = $params['type'];
        }
        $allMenus = (new SysMenuModel())->baseSelect($menuWhereArr, ['id', 'name', 'type', 'path']);

        foreach ($allMenus as &$v) {
            $v['have'] = in_array($v['id'], $menuIds) ? 1 : 0;
        }
        return self::ajax($allMenus);
    }

    public function saveRoleMenu($params, $uid)
    {
        if (empty($params['id'])) {
            return self::ajaxError('需要指定角色id');
        }
        if (empty($params['menu_ids'])) {
            $params['menu_ids'] = '';
        }
        try {
            // 先删除这个角色下的所有菜单
            (new SysRoleMenuModel())->baseDelete([
                'role_id' => $params['id']
            ]);

            if ($params['menu_ids'] != '') {
                $menuIds = explode(',', $params['menu_ids']);
                foreach ($menuIds as $v) {
                    (new SysRoleMenuModel())->baseInsert([
                        'role_id' => $params['id'],
                        'menu_id' => $v,
                        'create_time' => DateSupport::now(),
                        'create_user' => $uid
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
            $msg = "设置角色菜单时出现问题，msg:" . $e->getMessage();
            Log::error($msg);
            return self::ajaxError($msg);
        }
    }
}
