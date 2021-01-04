<?php


namespace App\Models;


use App\Models\Base\BaseDxeverModel;
use App\Support\ArraySupport;
use Illuminate\Support\Facades\DB;

class UserModel extends BaseDxeverModel
{
    public $table = 'user';

    public function getUserDailyStatistic($where = [], $order = []) : array
    {
        $q = DB::connection('mysql_dxever')->table($this->table)
            ->select(
                DB::raw("DATE_FORMAT(timestamp, '%Y-%m-%d') as d, count(uid) as c, sum(case when vip = 0 then 1 else 0 end) as vip0, sum(case when vip = 1 then 1 else 0 end) as vip1, sum(case when vip = 2 then 1 else 0 end) as vip2")
            )->where($where)->groupBy(['d']);
        if (empty($order['order'])) {
            $order['order'] = 'd';
        }
        if (empty($order['order_method'])) {
            $order['order_method'] = 'desc';
        }
        $q = $q->orderBy($order['order'], $order['order_method']);
        return ArraySupport::stdClassToArray($q->get());
    }
}
