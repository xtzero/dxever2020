<?php


namespace App\Http\Controllers\Fanli;


use App\Support\ArraySupport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DbMoveController
{
    public function index()
    {
        Log::info('开始同步数据库！');
        echo '开始同步数据库！';
        $tables = array_column(ArraySupport::stdClassToArray(DB::connection('mysql_dxever')->select('show tables;')), 'Tables_in_dxever');
        $existTables = array_column(ArraySupport::stdClassToArray(DB::select('show tables;')), 'Tables_in_dxever');
        Log::info("本次有".count($tables)."个表需要同步:" . implode(',', $tables));
        echo "本次有".count($tables)."个表需要同步:" . implode(',', $tables);
        Log::info('开始检查表同步');
        echo '开始检查表同步';
        foreach ($tables as $table) {
            if (!in_array($table, $existTables)) {
                Log::info('数据表不一致，请手动同步.table:' . $table);
                echo '数据表不一致，请手动同步.table:' . $table;
                return;
            }
        }
        Log::info('开始同步表数据');
        foreach ($tables as $table) {
            Log::info("清空表数据：{$table}");
            echo "清空表数据：{$table}";
            DB::table($table)->truncate();
            $count = DB::connection('mysql_dxever')->table($table)->count();
            Log::info("共{$count}条数据需要处理，分为".($count / 1000 + 1).'页');
            echo "共{$count}条数据需要处理，分为".($count / 1000 + 1).'页';
            for($i = 0; $i <= $count / 1000; $i ++) {
                Log::info("开始第".($i + 1)."页");
                echo "开始第".($i + 1)."页";
                $_data = ArraySupport::stdClassToArray(DB::connection('mysql_dxever')->table($table)->skip($i * 1000)->take(1000)->get());

                $_keys = array_keys($_data[0]);
                $_sql = "insert into {$table}(".implode(',', $_keys).") values ";
                $_valueArr = [];
                foreach ($_data as $_d) {
                    $_valueArr[] = "('".implode("','", array_values($_d))."')";
                }
                $_sql .= implode(',', $_valueArr). ';';
                $_insertSuccessCount = DB::insert($_sql);
                Log::info("第" . ($i + 1) . "页执行完毕，共".count($_data)."条数据，成功{$_insertSuccessCount}条");
                echo "第" . ($i + 1) . "页执行完毕，共".count($_data)."条数据，成功{$_insertSuccessCount}条";
            }
        }
        Log::info('全部执行完毕');
        echo '全部执行完毕';
    }
}
