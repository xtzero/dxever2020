<?php


namespace App\Service\Wx;


use App\Service\Base\BaseService;
use Illuminate\Support\Facades\URL;

class WxJsapiService extends BaseService
{
    public function wxAccess()
    {
        $ticket = Redis::get('ticket'.env('WX_APPID'));
        if (!$ticket) {
            $token = Redis::get('access_token'.env('WX_APPID'));
            if (!$token) {
                $token = $this->getAccessToken();
                if ($token) {
                    return $this->wxAccess();
                } else {
                    return false;
                }
            } else {
                $ticket = $this->getTicket($token);
                if (!$ticket) {
                    return false;
                } else {
                    return $this->wxAccess();
                }
            }
        } else {
            return $this->calcSignature($ticket);
        }
    }

    private function getAccessToken()
    {
        $_getAccessToken = file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='. env('WX_APPID') .'&secret='. env('WX_SECRET'));
        $getAccessToken = json_decode($_getAccessToken, true);
        $token = $getAccessToken['access_token'] ?? FALSE;
        if ($token) {
            Redis::setex('access_token'.env('WX_APPID'), 7000, $token);
            return $token;
        } else {
            return false;
        }
    }

    private function getTicket($token)
    {
        $_getTicket = file_get_contents('https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$token.'&type=jsapi');
        $getTicket = json_decode($_getTicket, true);
        if ($getTicket['errcode'] == 0) {
            $ticket = $getTicket['ticket'];
            Redis::setex('ticket'.env('WX_APPID'), 7000, $ticket);
            return $ticket;
        } else {
            return false;
        }
    }

    private function calcSignature($ticket)
    {
        // 该url为调用jssdk接口的url
        $url = URL::full();
        // 生成时间戳
        $timestamp = time();
        // 生成随机字符串
        $nonceStr = rand(100000, 999999);
        // 这里参数的顺序要按照 key 值 ASCII 码升序排序 j -> n -> t -> u
        $string = "jsapi_ticket={$ticket}&noncestr={$nonceStr}&timestamp={$timestamp}&url={$url}";
        $signature = sha1($string);
        $signPackage = array (
            "appId" => env('WX_APPID'),
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string,
            "ticket" => $ticket,
        );
        return $signPackage;
    }
}
