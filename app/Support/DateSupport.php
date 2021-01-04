<?php


namespace App\Support;


class DateSupport
{
    public static function now($format = 'Y-m-d H:i:s') {
        return date($format);
    }

    /**
     * 使用生日计算年龄
     * @param $birthday
     * @return bool|int
     */
    public static function birthdayToAge($birthday)
    {
        if (!$birthday) {
            return false;
        }
        $birYear = (int)date('Y', strtotime($birthday));
        $birMonth = (int)date('m', strtotime($birthday));
        if (!$birYear || !$birMonth) {
            return false;
        }
        $nowYear = (int)date('Y');
        $nowMonth = (int)date('m');
        $age = $nowYear - $birYear;
        if ($age < 0) {
            return false;
        }
        if ($age < 1) {
            return ($nowMonth - $birMonth) . '个月';
        }
        if ($nowMonth > $birMonth) {
            $age += 1;
        }
        return (int)$age . '岁';
    }
}
