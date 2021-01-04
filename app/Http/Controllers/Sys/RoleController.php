<?php


namespace App\Http\Controllers\Sys;


use App\Http\Controllers\Base\AppController;
use App\Service\Sys\RoleService;
use Illuminate\Http\Request;

class RoleController extends AppController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function roleList()
    {
        $this->param([
            'name' => ['format' => 'string']
        ]);
        return (new RoleService())->roleList($this->params);
    }

    public function saveRole()
    {
        $this->param([
            'id' => ['format' => 'int'],
            'name' => ['format' => 'string']
        ]);
        return (new RoleService())->saveRole($this->params, $this->uid);
    }

    public function deleteRole()
    {
        $this->param([
            'id' => ['format' => 'int']
        ]);

        return (new RoleService())->deleteRole($this->params, $this->uid);
    }

    public function getRoleMenuList()
    {
        $this->param([
            'id' => ['format' => 'int'],
            'type' => ['format' => 'string']
        ]);
        return (new RoleService())->getRoleMenuList($this->params);
    }

    public function saveRoleMenu()
    {
        $this->param([
            'id' => ['format' => 'int'],
            'menu_ids' => ['format' => 'string']
        ]);
        return (new RoleService())->saveRoleMenu($this->params, $this->uid);
    }
}
