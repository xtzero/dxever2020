<?php


namespace App\Http\Controllers\Fanli;


use App\Http\Controllers\Base\AppController;
use App\Service\Fanli\UserService;
use Illuminate\Http\Request;

class UserController extends AppController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    /**
     * 用户列表
     * @return false|string
     */
    public function userList()
    {
        $this->param([
            'nickname' => ['format' => 'string'],
            'openid' => ['format' => 'string'],
            'limit' => ['format' => 'int', 'default' => 20],
            'page' => ['format' => 'int', 'default' => 1]
        ]);

        return (new UserService())->userList($this->params);
    }

    /**
     * 商户列表
     * @return false|string
     */
    public function businessList()
    {
        $this->param([
            'name' => ['format' => 'string'],
            'limit' => ['format' => 'int', 'default' => 20],
            'page' => ['format' => 'int', 'default' => 1]
        ]);

        return (new UserService())->businessList($this->params);
    }

    /**
     * 商家详情
     * @return false|string
     */
    public function businessDetail()
    {
        $this->param([
            'bid' => ['format' => 'int', 'require' => true]
        ]);

        return (new UserService())->businessDetail($this->params);
    }
}
