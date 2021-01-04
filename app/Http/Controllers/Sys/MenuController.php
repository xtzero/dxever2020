<?php


namespace App\Http\Controllers\Sys;


use App\Http\Controllers\Base\AppController;
use App\Service\Sys\MenuService;
use Illuminate\Http\Request;

class MenuController extends AppController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function menuList()
    {
        $this->param([
            'name' => ['format' => 'string'],
            'type' => ['format' => 'string', 'range' => ['', 'api', 'page']]
        ]);

        return (new MenuService())->menuList($this->params);
    }

    public function saveMenu()
    {
        $this->param([
            'id' => ['format' => 'int'],
            'name' => ['format' => 'string'],
            'type' => ['format' => 'string', 'range' => ['api', 'page']],
            'path' => ['format' => 'string'],
            'icon' => ['format' => 'string'],
            'title' => ['format' => 'string'],
            'hidden' => ['format' => 'int', 'default' => 0],
            'order' => ['format' => 'int', 'default' => 1]
        ]);

        return (new MenuService())->saveMenu($this->params, $this->uid);
    }

    public function deleteMenu()
    {
        $this->param([
            'id' => ['format' => 'int']
        ]);
        return (new MenuService())->deleteMenu($this->params, $this->uid);
    }

    public function menuRoleList()
    {
        $this->param([
            'id' => ['format' => 'int']
        ]);

        return (new MenuService())->menuRoleList($this->params);
    }

    public function saveMenuRole()
    {
        $this->param([
            'id' => ['format' => 'int'],
            'role_ids' => ['format' => 'string']
        ]);

        return (new MenuService())->saveMenuRole($this->params, $this->uid);
    }
}
