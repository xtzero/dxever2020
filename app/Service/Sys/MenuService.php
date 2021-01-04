<?php


namespace App\Service\Sys;


use App\Models\SysMenuModel;
use App\Models\SysRoleMenuModel;
use App\Models\SysRoleModel;
use App\Service\Base\BaseService;
use App\Support\ArraySupport;
use App\Support\DateSupport;
use Illuminate\Support\Facades\Log;

class MenuService extends BaseService
{
    public function menuList($params)
    {
        $whereArr = [
            ['is_del', '=', 0]
        ];
        if (!empty($params['name'])) {
            $whereArr[] = ['name', 'like', "%{$params['name']}%"];
        }
        if (!empty($params['type'])) {
            $whereArr[] = ['type', '=', $params['type']];
        }

        return self::ajax(ArraySupport::stdClassToArray((new SysMenuModel())->baseQuery($whereArr)->orderByDesc('create_time')->get()));
    }

    public function saveMenu($params, $uid)
    {
        if (isset($params['id'])) {
            return $this->editMenu($params, $uid);
        } else {
            return $this->addMenu($params, $uid);
        }
    }

    private function addMenu($params, $uid)
    {
        $saveData = [];
        if (!empty($params['name'])) {
            $saveData['name'] = $params['name'];
        } else {
            return self::ajaxError('需要菜单名称');
        }
        if (!empty($params['type'])) {
            $saveData['type'] = $params['type'];
        } else {
            return self::ajaxError('需要菜单方式');
        }
        if (!empty($params['path'])) {
            $saveData['path'] = $params['path'];
        } else {
            return self::ajaxError('需要菜单路径');
        }
        if ($params['type'] == 'page') {
            if (!empty($params['icon'])) {
                $saveData['icon'] = $params['icon'];
            } else {
                return self::ajaxError('需要菜单图标');
            }
            if (!empty($params['title'])) {
                $saveData['title'] = $params['title'];
            } else {
                return self::ajaxError('需要页面标题');
            }
            if (!empty($params['hidden'])) {
                $saveData['hidden'] = $params['hidden'];
            } else {
                return self::ajaxError('需要指明页面是否隐藏');
            }
            if (!empty($params['order'])) {
                $saveData['order'] = $params['order'];
            } else {
                return self::ajaxError('需要指定页面排序');
            }
        }

        try {
            $insert = (new SysMenuModel())->baseInsert(array_merge($saveData, [
                'create_user' => $uid,
                'create_time' => DateSupport::now(),
                'is_del' => 0
            ]));
            return self::ajax([
                'insert' => $insert
            ]);
        } catch (\Exception $e) {
            $msg = "保存菜单时出现问题，msg:" . $e->getMessage();
            Log::error($msg);
            return self::ajaxError($msg);
        }
    }

    private function editMenu($params, $uid)
    {
        $saveData = [];
        if (empty($params['id'])) {
            return self::ajaxError('需要指定要修改的菜单');
        }
        $menu = (new SysMenuModel())->baseSelectFirst([
            'id' => $params['id'],
            'is_del' => 0
        ]);
        if (!$menu) {
            return self::ajaxError('这个菜单不存在');
        }
        if (isset($params['name']) && $params['name'] != $menu['name']) {
            $saveData['name'] = $params['name'];
        }
        if (isset($params['type']) && $params['type'] != $menu['type']) {
            $menu['type'] = $params['type'];
            $saveData['type'] = $params['type'];
        }
        if (isset($params['path']) && $params['path'] != $menu['path']) {
            $saveData['path'] = $params['path'];
        }
        if ($menu['type'] == 'page') {
            if (isset($params['icon']) && $params['icon'] != $menu['icon']) {
                $saveData['icon'] = $params['icon'];
            }
            if (isset($params['title']) && $params['title'] != $menu['title']) {
                $saveData['title'] = $params['title'];
            }
            if (isset($params['hidden']) && $params['hidden'] != $menu['hidden']) {
                $saveData['hidden'] = $params['hidden'];
            }
            if (isset($params['order']) && $params['order'] != $menu['order']) {
                $saveData['order'] = $params['order'];
            }
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
            $update = (new SysMenuModel())->baseUpdate([
                'id' => $params['id']
            ], $saveData);
            return self::ajax([
                'update' => $update
            ]);
        } catch (\Exception $e) {
            $msg = "保存菜单时出现问题，msg:" . $e->getMessage();
            Log::error($msg);
            return self::ajaxError($msg);
        }
    }

    public function deleteMenu($params, $uid)
    {
        if (empty($params['id'])) {
            return self::ajaxError('需要指定要修改的菜单');
        }
        $menu = (new SysMenuModel())->baseSelectFirst([
            'id' => $params['id'],
            'is_del' => 0
        ]);
        if (!$menu) {
            return self::ajaxError('这个菜单不存在');
        }
        try {
            $update = (new SysMenuModel())->baseUpdate([
                'id' => $params['id']
            ], [
                'modify_user' => $uid,
                'modify_time' => DateSupport::now(),
                'is_del' => 1
            ]);
            return self::ajax([
                'update' => $update
            ]);
        } catch (\Exception $e) {
            $msg = "删除菜单时出现问题，msg:" . $e->getMessage();
            Log::error($msg);
            return self::ajaxError($msg);
        }
    }

    public function menuRoleList($params)
    {
        if (empty($params['id'])) {
            return self::ajaxError('需要菜单id');
        }

        $roleMenus = (new SysRoleMenuModel())->baseSelect([
            'menu_id' => $params['id']
        ], ['role_id']);
        if (empty($roleMenus)) {
            return self::ajax([]);
        } else {
            $roleIds = array_column($roleMenus, 'role_id');
            $roles = ArraySupport::stdClassToArray((new SysRoleModel())->baseQuery([
                'is_del' => 0
            ], ['id', 'name'])->whereIn('id', $roleIds)->get());
            return self::ajax($roles);
        }
    }

    public function saveMenuRole($params, $uid)
    {
        if (empty($params['id'])) {
            return self::ajaxError('需要指定菜单id');
        }
        if (empty($params['role_ids'])) {
            return self::ajaxError('需要指定要保存的菜单');
        }
        try {
            // 先删除这个角色下的所有菜单
            (new SysRoleMenuModel())->baseDelete([
                'menu_id' => $params['id']
            ]);
            $menuIds = explode(',', $params['role_ids']);
            foreach ($menuIds as $v) {
                (new SysRoleMenuModel())->baseInsert([
                    'role_id' => $v,
                    'menu_id' => $params['id'],
                    'create_time' => DateSupport::now(),
                    'create_user' => $uid
                ]);
            }
            return self::ajax([
                'res' => 1
            ]);
        } catch (\Exception $e) {
            $msg = "设置角色菜单时出现问题，msg:" . $e->getMessage();
            Log::error($msg);
            return self::ajaxError($msg);
        }
    }
}
