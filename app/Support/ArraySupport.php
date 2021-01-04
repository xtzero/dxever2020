<?php
namespace App\Support;

class ArraySupport
{
    /**
     * 数据库对象转数组
     * @param $std
     * @return mixed
     */
    public static function stdClassToArray($std)
    {
        return json_decode(json_encode($std), true);
    }

    public static function xtEncode($arr)
    {
        $arr_ = [];
        foreach ($arr as $k => $v) {
            $arr_[] = implode('|__|', $v);
        }
        return implode('__--__', $arr_);
    }

    public static function xtDecode($str)
    {
        $arr = explode('__--__', $str);
        $resArr = [];
        foreach ($arr as $k => $v) {
            $resArr[] = explode('||||', $v);
        }
        return $resArr;
    }

    /**
     * 排除空值
     * @param $arr
     * @return array
     */
    public static function exceptEmptyValue($arr)
    {
        $resArr = [];
        foreach ($arr as $k => $v) {
            if ($v != '' && !empty($v) && !is_null($v)) {
                $resArr[] = $v;
            }
        }
        return $resArr;
    }
}
