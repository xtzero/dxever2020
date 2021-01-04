<?php
namespace App\Service\Index;

use App\Models\UserModel;
use App\Service\Base\BaseService;
use App\Support\ArraySupport;
use Illuminate\Support\Facades\DB;

class IndexService extends BaseService
{
    public function index()
    {
        return '别闹了，我偷电动车养你';
    }

    public function homepage()
    {
        $userCount = ArraySupport::stdClassToArray(DB::connection('mysql_dxever')->table((new UserModel())->table)
            ->select(DB::raw('count(uid) as c, vip'))
            ->groupBy(['vip'])
            ->orderBy(DB::raw('c'))
            ->get());

        $userInc = ArraySupport::stdClassToArray(DB::connection('mysql_dxever')->select("select sum(case when vip = 0 then 1 else 0 end) as vip0, sum(case when vip = 1 then 1 else 0 end) as vip1, sum(case when vip = 2 then 1 else 0 end) as vip2, sum(case when vip = 3 then 1 else 0 end) as vip3 from `user` where date_format(timestamp, '%Y-%m-%d') = '2020-11-09'"))[0];


        return self::ajax([
            'userCount' => $userCount,
            'userInc' => $userInc
        ]);
    }
}
