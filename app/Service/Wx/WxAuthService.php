<?php


namespace App\Service\Wx;


use App\Service\Base\BaseService;
use Illuminate\Support\Facades\Redis;

class WxAuthService extends BaseService
{
    public function wxauth($code, $redirectUrl)
    {
        if (!$code) {
            $accessUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.env('WX_APPID').'&redirect_uri='.urlencode($redirectUrl).'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
            return redirect($accessUrl);
        }
        // 有code的时候调微信接口取
        else {
            $getAccessToken = $this->wxGetAccessToken($code, $redirectUrl);
            if (!$getAccessToken) {
                return redirect($redirectUrl);
            }
            $openid = $getAccessToken['openid'];
            $token = $getAccessToken['token'];

            $userinfo = Redis::get('wxUserinfo_'.$openid);
            if (!$userinfo) {
                $userinfo = $this->wxGetUserinfo($token, $openid);
            } else {
                $userinfo = json_decode($userinfo, TRUE);
            }
            return $userinfo;
        }
    }

    public function wxGetAccessToken($code, $redirectUrl)
    {
        // 先从redis取
        $getAccessTokenFromRedis = Redis::get('code_to_accesstoken_'.$code);
        if ($getAccessTokenFromRedis) {
            $accesstokenAndOpenid = explode('|', $getAccessTokenFromRedis);
            return [
                'token' => $accesstokenAndOpenid[0],
                'openid' => $accesstokenAndOpenid[1]
            ];
        }

        $url = implode('',[
            'https://api.weixin.qq.com/sns/oauth2/access_token?appid=',
            env('WX_APPID'),
            '&secret=',
            env('WX_SECRET'),
            '&code=',
            $code,
            '&grant_type=authorization_code'
        ]);
        $res = file_get_contents($url);
        if ($res) {
            $resArr = json_decode($res, TRUE);
            if (isset($resArr['errcode'])) {
                return view('Error.error', [
                    'message' => '微信认证时出现问题：'.$resArr['errmsg'].'，3秒后为你跳转',
                    'func' => "window.location.href='".$redirectUrl."'",
                    'timeout' => 3000,
                ]);
            } else {
                Redis::setex('code_to_accesstoken_'.$code, 7200, $resArr['access_token'].'|'.$resArr['openid']);
                return [
                    'token' => $resArr['access_token'],
                    'openid' => $resArr['openid']
                ];
            }
        } else {
            return view('Error.error', [
                'message' => '微信认证时出现问题：'.$res.'，3秒后重试',
                'func' => "history.back()",
                'timeout' => 3000,
            ]);
        }
    }

    public function wxGetUserinfo($token, $openid)
    {
        $url = implode('', [
            'https://api.weixin.qq.com/sns/userinfo?access_token=',
            $token,
            '&openid=',
            $openid,
            '&lang=zh_CN'
        ]);
        $res = file_get_contents($url);
        if ($res) {
            $resArr = json_decode($res, TRUE);
            if (isset($resArr['errcode'])) {
                return view('Error.error', [
                    'message' => '拉取用户信息时出现问题：'.$resArr['errmsg'].'，3秒后重试',
                    'func' => "history.back()",
                    'timeout' => 3000,
                ]);
            } else {
                Redis::setex('wxUserinfo_'.$openid, 86400, json_encode($resArr));
                return $resArr;
            }
        } else {
            return view('Error.error', [
                'message' => '拉取用户信息时出现问题：'.$res.'，3秒后重试',
                'func' => "history.back()",
                'timeout' => 3000,
            ]);
        }
    }
}
