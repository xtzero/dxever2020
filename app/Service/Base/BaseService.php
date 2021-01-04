<?php


namespace App\Service\Base;


use App\Support\AjaxSupport;
use App\Support\LogSupport;

class BaseService
{
    public static function ajax() {
        return AjaxSupport::ajax(...func_get_args());
    }

    public static function ajaxError($msg, $code = 500) {
        LogSupport::notice($msg);
        return self::ajax($msg, $code);
    }
}
