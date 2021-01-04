<?php


namespace App\Support;


use TencentCloud\Common\Credential;
use \TencentCloud\Sms\V20190711\Models\SendSmsRequest;
use TencentCloud\Sms\V20190711\SmsClient;

class QcoundSupport
{
    public static function sendVerifyCode($mobile, $code)
    {
        $cred = new Credential(env('QCLOUD_SECRET_ID'), env('QCOUND_SECRET_KEY'));
        $client = new SmsClient($cred, "ap-shanghai");
        $req = new SendSmsRequest();
        $req->SmsSdkAppid = env('QCLOUD_SMS_APPID');
        $req->Sign = env('QCLOUD_SMS_SIGN');
        $req->ExtendCode = "0";
        $req->PhoneNumberSet = array("+86{$mobile}");
        $req->TemplateID = "529607";
        $req->TemplateParamSet = array("0" => $code);

        $resp = $client->SendSms($req);
        return json_decode($resp->toJsonString(), true);
    }
}
