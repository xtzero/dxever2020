<?php
namespace App\Http\Controllers\Index;

use App\Http\Controllers\Base\AppController;
use App\Service\Index\IndexService;
use Illuminate\Http\Request;

class IndexController extends AppController
{
    protected $bid = 0;

    public function __construct(Request $request)
    {
        if (!$request->input('bid', null)) {
            $this->ajaxError('需要商家id');
        }
        $this->bid = $request->input('bid');
        parent::__construct($request);
    }

    public function ifBusinessExist()
    {
        return (new IndexService())->ifBusinessExist($this->bid);
    }

    public function baseInfo()
    {
        return (new IndexService())->baseInfo($this->bid);
    }

    public function userList()
    {
        $this->param([
            'page' => ['format' => 'int'],
            'limit' => ['format' => 'int']
        ]);
        return (new IndexService())->userList($this->params, $this->bid);
    }

    public function orderList()
    {
        $this->param([
            'nickname' => ['format' => 'string'],
            'openid' => ['format' => 'string'],
            'date_begin' => ['format' => 'string'],
            'date_end' => ['format' => 'string'],
            'page' => ['format' => 'int'],
            'limit' => ['format' => 'int']
        ]);

        return (new IndexService())->orderList($this->params, $this->bid);
    }

    public function dailyStatistic()
    {
        $this->param([
            'date_begin' => ['format' => 'string'],
            'date_end' => ['format' => 'string']
        ]);
        return (new IndexService())->dailyStatistic($this->params, $this->bid);
    }
}
