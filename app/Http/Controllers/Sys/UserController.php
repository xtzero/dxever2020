<?php


namespace App\Http\Controllers\Sys;


use App\Http\Controllers\Base\AppController;
use App\Service\Sys\UserService;
use Illuminate\Http\Request;

class UserController extends AppController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function sysUserList()
    {
        $this->param([
            'username' => ['format' => 'string'],
            'name' => ['format' => 'string']
        ]);
        return (new UserService())->sysUserList($this->params);
    }

    public function saveSysUser()
    {
        $this->param([
            'uid' => ['format' => 'int'],
            'username' => ['format' => 'string'],
            'password' => ['format' => 'string'],
            'name' => ['format' => 'string']
        ]);

        return (new UserService())->saveSysUser($this->params, $this->uid);
    }

    public function deleteSysUser()
    {
        $this->param([
            'uid' => ['format' => 'int']
        ]);

        return (new UserService())->deleteSysUser($this->params, $this->uid);
    }

    public function setUserRole()
    {
        $this->param([
            'uid' => ['format' => 'int'],
            'role_ids' => ['format' => 'string'],
        ]);
        return (new UserService())->setUserRole($this->params, $this->uid);
    }
}
