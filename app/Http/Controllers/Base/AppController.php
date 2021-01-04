<?php

namespace App\Http\Controllers\Base;

use App\Models\SysMenuModel;
use App\Models\SysRoleMenuModel;
use App\Models\SysRoleUserModel;
use App\Models\SysUserModel;
use App\Support\IpSupport;
use App\Support\LogSupport;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Support\AjaxSupport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

class AppController extends BaseController
{
    public $request;
    public $params = [];
    public $uid = 0;
    public $userinfo = [];
    public $currentApi = '';

    public function __construct(Request $request)
    {
        $this->request = $request;
        $whiteList = config('whitelist');
        $this->currentApi = str_replace('App\\Http\\Controllers\\', '', Route::currentRouteAction());
        if (!in_array($this->currentApi, $whiteList['notNeedSign'])) {
            $this->checkSign($request);
        }
        if (!in_array($this->currentApi, $whiteList['notNeedLogin'])) {
            $this->checkLogin($request);
            if (!in_array($this->currentApi, $whiteList['notNeedAuth'])) {
                $this->checkAuth();
            }
        }
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

            if (isset($v['range'])) {
                if (!in_array($value, $v['range'])) {
                    $this->ajaxError('range error! range:' . implode(',', $v['range']) . ',value:' . $value , 50004);
                }
            }
            if ($value) {
                switch ($v['format']) {
                    case 'int': $value = (int)$value; break;
                    case 'string': $value = (string)$value; break;
                    case 'json2arr': $value = json_decode($value, true) ?? $value; break;
                    case 'arr2json': $value = json_encode($value) ?? $value; break;
                    case 'bool': $value = $value ? true : false; break;
                }
            }

            if ($value) {
                $this->params[$k] = $value;
            }

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
    public function checkSign(Request $req) : bool
    {
        $params = $req->input();
        $sign = $params['sign'] ?? false;
        if (!$sign) {
            $this->ajax('need sign', 50000);
        }
        unset($params['sign']);
        ksort($params);
        $signkey = env('API_SIGN_KEY');
        foreach ($params as $k => &$v) {
            $v = $v ? (string)$v : 'empty-value';
        }
        $paramStr = implode(',', $params) . $signkey;
        $signs = [];
        for ($i = 0; $i < 100; $i ++) {
            $signs[] = md5(md5($paramStr) . $i);
        }
        if (!in_array($sign, $signs)) {
            $this->ajax('sign error: ' . $sign);
        }

        return true;
    }

    /**
     * 检查登录
     */
    public function checkLogin(Request $req)
    {
        $token = $req->header('access-token', null);
        if (!$token) {
            $this->ajax('need token', 50002);
        }
        $tokenData = Redis::get(config('keys.redisKey.tokenPrefix').$token);
        if (!$tokenData) {
            $this->ajax('token error', 50002);
        }
        $userInfo = unserialize($tokenData);
        if (empty($userInfo) || empty($userInfo['uid'])) {
            $this->ajax('userinfo error', 50002);
        }
        $userinfo = (new SysUserModel())->baseSelectFirst([
            'uid' => $userInfo['uid']
        ]);
        $this->uid = $userinfo['uid'];
        $this->userinfo = $userinfo;
        return true;
    }

    public function checkAuth()
    {
        $menus = (new SysRoleMenuModel())->getMenuIdsByUid($this->userinfo['uid']);
        if (!in_array($this->currentApi, $menus)) {
            $this->ajax('无权限，请联系管理员开通', 50003);
        }
    }

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
