<?php


namespace App\Http\Controllers\Fanli;


use App\Http\Controllers\Base\AppController;
use App\Service\Fanli\StatisticService;
use Illuminate\Http\Request;

class StatisticController extends AppController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function userDailyStatistic()
    {
        $this->param([
            'date_begin' => ['format' => 'string'],
            'date_end' => ['format' => 'string'],
            'order' => ['format' => 'string', 'range' => ['c', 'd', 'vip0', 'vip1', 'vip2'], 'default' => 'c'],
            'order_method' => ['format' => 'string', 'range' => ['desc', 'asc'], 'default' => 'desc']
        ]);

        return (new StatisticService())->userDailyStatistic($this->params);
    }

    public function userOrderRank()
    {
        $this->param([
            'date_begin' => ['format' => 'string'],
            'date_end' => ['format' => 'string'],
            'page' => ['format' => 'string', 'default' => 1],
            'limit' => ['format' => 'int', 'default' => 20]
        ]);
        return (new StatisticService())->userOrderRank($this->params);
    }

    public function businessOrderRank()
    {
        $this->param([
            'date_begin' => ['format' => 'string'],
            'date_end' => ['format' => 'string'],
            'page' => ['format' => 'string', 'default' => 1],
            'limit' => ['format' => 'int', 'default' => 20]
        ]);
        return (new StatisticService())->businessOrderRank($this->params);
    }

    public function orderDailySummary()
    {
        $this->param([
            'date_begin' => ['format' => 'string'],
            'date_end' => ['format' => 'string']
        ]);

        return (new StatisticService())->orderDailySummary($this->params);
    }
}
