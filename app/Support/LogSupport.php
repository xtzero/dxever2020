<?php


namespace App\Support;


use Illuminate\Support\Facades\Log;

class LogSupport
{
    public static function info()
    {
        Log::channel('xtInfo')->info(...func_get_args());
    }

    public static function error()
    {
        Log::channel('xtError')->error(...func_get_args());
    }

    public static function notice()
    {
        Log::channel('xtNotice')->notice(...func_get_args());
    }

    public static function debug()
    {
        Log::channel('xtDebug')->debug(...func_get_args());
    }
    public static function calcFileSize($size)
    {
        if ($size > 1000) {
            return round($size / 1024, 0) . ' kb';
        } else if ($size > 1000000 ) {
            return round($size / 1024 / 1024, 0) . ' mb';
        } else if ($size > 1000000000) {
            return round($size / 1024 / 1024 / 1024, 0) . ' gb';
        } else {
            return $size . 'b';
        }
    }
}
