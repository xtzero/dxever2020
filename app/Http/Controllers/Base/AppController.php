<?php

namespace App\Http\Controllers\Base;

use App\Support\IpSupport;
use App\Support\LogSupport;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Support\AjaxSupport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class AppController extends BaseController
{
    public $request;
    public $params = [];
    public $uid = 0;
    public $userinfo = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->behaviorLog();
    }

    /**
     * 参数验证方法
     */
    public function param($params)
    {
        $needButNone = [];
        foreach ($params as $k => $v) {
            $value = $this->request->input($k, null);
            if (isset($v['require']) && $v['require'] === true) {
                if (is_null($value)) {
                    $needButNone[] = $k;
                }
            } else {
                $value = $value ?? $v['default'] ?? null;
            }

            switch ($v['format']) {
                case 'int': $value = (int)$value; break;
                case 'string': $value = (string)$value; break;
                case 'json2arr': $value = json_decode($value, true) ?? $value; break;
                case 'arr2json': $value = json_encode($value) ?? $value; break;
                case 'bool': $value = $value ? true : false; break;
            }

            $this->params[$k] = $value;
        }

        if (!empty($needButNone)) {
            $this->ajaxError('need params: ' . implode(',', $needButNone), 50004);
        }
    }

    /**
     * ajax返回方法
     */
    public function ajax() {
        echo AjaxSupport::ajax(...func_get_args());
        die();
    }

    /**
     * ajax返回错误
     * @param $msg
     * @param int $code
     */
    public function ajaxError($msg, $code = 500) {
        $this->ajax($msg, $code);
    }

    /**
     * 验证签名
     */
    public function checkSign()
    {
        $sign = $this->request->input('sign');
        if (!$sign) {
            $this->ajax('需要接口签名 sign', 50003);
        }
        $params = $this->params;
        ksort($params);
        $signArr = [];
        foreach ($params as $k => $v) {
            $signArr[] = "{$k}={$v}";
        }
        $signArr[] = "secret=".env('SIGN_SECRET');
        $correctSign = md5(implode('&', $signArr));
        if ($sign != $correctSign) {
            $this->ajax('接口签名错误', 50003);
        }
    }

    /**
     * 检查登录
     */
    public function checkLogin($require = true)
    {
        $token = $this->request->input('access_token');
        if (!$token) {
            if (!$require) {
                return;
            } else {
                $this->ajax('need token', 50002);
            }
        }
        $tokenData = Redis::get(config('keys.redisKey.tokenPrefix').$token);
        if (!$tokenData) {
            if (!$require) {
                return;
            } else {
                $this->ajax('token error', 50002);
            }
        }
        $userInfo = unserialize($tokenData);
        if (empty($userInfo) || empty($userInfo['id'])) {
            if (!$require) {
                return;
            } else {
                $this->ajax('userinfo error', 50002);
            }
        }
        $this->uid = $userInfo['id'];
        $this->userinfo = $userInfo;
        $this->params['uid'] = $userInfo['id'];
    }

    /**
     * 检查是不是管理员
     */
//    public function checkAdmin()
//    {
//        if ($this->userinfo['user_type'] != 'admin') {
//            AjaxSupport::ajax('只有管理员可以使用', 50003);
//        }
//    }

    /**
     * 检查是不是普通用户
     */
//    public function checkIsUser()
//    {
//        if ($this->userinfo['user_type'] != 'normal') {
//            AjaxSupport::ajax('只有普通用户可以使用', 50003);
//        }
//    }

    public function behaviorLog()
    {
        LogSupport::debug(json_encode([
            "ipFromLaravel" => $this->request->getClientIps(),
            "ipFromSupport" => IpSupport::getIp(),
            "params" => $this->request->input(),
            "url" => $this->request->fullUrl(),
            "method" => $this->request->method(),
            'comment' => $this->request->route()->getName()
        ], JSON_UNESCAPED_UNICODE));
    }
}
