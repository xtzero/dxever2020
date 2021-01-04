<?php


namespace App\Models;


use App\Models\Base\BaseModel;
use App\Support\ArraySupport;
use Illuminate\Support\Facades\DB;

class SysRoleMenuModel extends BaseModel
{
    public $table = 'sys_role_menu';

    public function getMenuIdsByUid($uid)
    {
        $userRoles = (new SysRoleUserModel())->baseSelect([
            'uid' => $uid
        ]);
        $roles = array_column($userRoles, 'role_id');
        $roleMenus = Db::table((new SysRoleMenuModel())->table)
            ->whereIn('role_id', $roles)
            ->leftJoin((new SysMenuModel())->table, (new SysMenuModel())->table . '.id', '=', (new SysRoleMenuModel())->table . '.menu_id')
            ->select([(new SysMenuModel())->table . '.path'])
            ->get();
        return array_column(ArraySupport::stdClassToArray($roleMenus), 'path');
    }
}
