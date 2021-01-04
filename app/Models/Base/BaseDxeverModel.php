<?php
namespace App\Models\Base;

use App\Support\LogSupport;
use Illuminate\Support\Facades\DB;
use App\Support\ArraySupport;
class BaseDxeverModel
{
    /**
     * @var 表名
     */
    public $table;

    /**
     * 标识删除的键
     * @var string
     */
    protected $delKey = 'is_del';
    /**
     * 表示删除的值
     * @var int
     */
    protected $delValue = 1;

    /**
     * 插入
     * @param $data
     * @return int
     */
    public function baseInsert($data)
    {
        return DB::connection('mysql_dxever')->table($this->table)->insertGetId($data);
    }

    /**
     * 假删
     * @param array $where
     * @return int
     */
    public function baseFakeDelete($where = [], $extraUpdateData = [])
    {
        $updateData = [
            $this->delKey => $this->delValue
        ];
        if ($extraUpdateData) {
            $updateData = array_merge($updateData, $extraUpdateData);
        }
        return DB::connection('mysql_dxever')->table($this->table)->where($where)->update($updateData);
    }

    /**
     * 删除
     * @param array $where
     * @return int
     */
    public function baseDelete($where = [])
    {
        return DB::connection('mysql_dxever')->table($this->table)->where($where)->delete();
    }

    /**
     * 更新
     * @param array $where
     * @param array $data
     * @return int
     */
    public function baseUpdate($where = [], $data = [])
    {
        return DB::connection('mysql_dxever')->table($this->table)->where($where)->update($data);
    }

    /**
     * 查询 返回数据库查询对象
     * @param array $where
     * @param array $fields
     * @return \Illuminate\Database\Query\Builder
     */
    public function baseQuery($where = [], $fields = [])
    {
        return DB::connection('mysql_dxever')->table($this->table)->where($where)->select($fields);
    }

    /**
     * 查询
     * @param array $where
     * @param array $fields
     * @return mixed
     */
    public function baseSelect($where = [], $fields = [])
    {
        return ArraySupport::stdClassToArray($this->baseQuery($where, $fields)->get());
    }

    /**
     *
     * @param array $where
     * @param array $fields
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function baseSelectFirst($where = [], $fields = [])
    {
        return  ArraySupport::stdClassToArray($this->baseQuery($where, $fields)->first());
    }

    /**
     * 查询单个值
     * @param array $where
     * @param string $field
     * @return mixed
     */
    public function baseSelectValue($where = [], $field = '')
    {
        return ArraySupport::stdClassToArray(DB::connection('mysql_dxever')->table($this->table)->where($where)->value($field));
    }

    /**
     * 查询数量
     * @param array $where
     * @return int
     */
    public function baseSelectCount($where = [])
    {
        return DB::connection('mysql_dxever')->table($this->table)->where($where)->select('count(*)')->count();
    }

    /**
     * 按分页查询
     * @param array $where
     * @param array $fields
     * @param int $limit
     * @param int $page
     * @return \Illuminate\Database\Query\Builder
     */
    public function baseSelectByPage($where = [], $fields = [], $limit = 20, $page = 1)
    {
        return $this->baseQuery($where, $fields)->skip(($page - 1) * $limit)->take($limit);
    }

    /**
     * 自增
     * @param array $where
     * @param string $column
     * @param int $step
     * @return int
     */
    public function baseInc($where = [], $column = '', $step = 1)
    {
        return DB::connection('mysql_dxever')->table($this->table)->where($where)->increment($column, $step);
    }

    /**
     * 自减
     * @param array $where
     * @param string $column
     * @param int $step
     * @return int
     */
    public function baseDec($where = [], $column = '', $step = 1)
    {
        return DB::connection('mysql_dxever')->table($this->table)->where($where)->decrement($column, $step);
    }

    /**
     * 事务
     * @param $queryColure
     */
    public function baseTransaction($queryColure) {
        DB::connection('mysql_dxever')->transaction($queryColure);
    }

    /**
     * 创建索引数组
     * @param array $idArr
     * @param string $idColumnName
     * @param string $arrKey
     * @param array $extraWhere
     * @return array
     */
    public function baseCreateDictionary($idArr = [], $idColumnName = 'id', $arrKey = 'id', $fields = ['*'], $extraWhere = [])
    {
        if (!$idArr) {
            return [];
        }
        $query = Db::connection('mysql_dxever')->table($this->table)
            ->select($fields)
            ->whereIn($idColumnName, $idArr);
        if ($extraWhere) {
            $query = $query->where($extraWhere);
        }
        $data = ArraySupport::stdClassToArray($query->get());
        if ($data) {
            return array_column($data, null, $arrKey);
        } else {
            return [];
        }
    }

    public function baseCreateGroupDictionary($idArr = [], $idColumnName = 'id', $arrKey = 'id', $fields = ['*'], $extraWhere = [])
    {
        if (!$idArr) {
            return [];
        }
        $whereArr = [
            [$idColumnName, 'in', $idArr]
        ];
        if ($extraWhere) {
            $whereArr = array_merge($whereArr, $extraWhere);
        }
        $data = $this->baseSelect($whereArr, $fields);
        if ($data) {
            $resArr = [];
            foreach ($data as $k => $v) {
                $resArr[$v[$arrKey]][] = $v;
            }
            return $resArr;
        } else {
            return [];
        }
    }

    /**
     * 创建分组的索引数组
     * @param array $idArr
     * @param string $idColumnName
     * @param string $arrKey
     * @param array $extraWhere
     * @return array
     */
    public function baseCreateDictionaryGroup($idArr = [], $idColumnName = 'id', $arrKey = 'id', $fields = ['*'], $extraWhere = [])
    {
        if (!$idArr) {
            return [];
        }
        $whereArr = [
            [$idColumnName, 'in', $idArr]
        ];
        if ($extraWhere) {
            $whereArr = array_merge($whereArr, $extraWhere);
        }
        $data = $this->baseSelect($whereArr, $fields);
        $resArr = [];
        foreach ($data as $k => $v) {
            $resArr[$v[$arrKey]][] = $v;
        }
        return $resArr;
    }
}
