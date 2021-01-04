<?php


namespace App\Service\Wx;


use App\Service\Base\BaseService;
use TencentCloud\Common\Credential;
use TencentCloud\Sms\V20190711\Models\SendSmsRequest;
use TencentCloud\Sms\V20190711\SmsClient;

class QcloudSmsService extends BaseService
{
    public $req;
    public $client;

    public function __construct()
    {
        $cred = new Credential(env('QCLOUD_SECRET_ID'), env('QCOUND_SECRET_KEY'));
        $client = new SmsClient($cred, "ap-shanghai");
        $req = new SendSmsRequest();
        /* 短信应用 ID: 在 [短信控制台] 添加应用后生成的实际 SDKAppID，例如1400006666 */
        $req->SmsSdkAppid = env('QCLOUD_SMS_APPID');
        /* 短信签名内容: 使用 UTF-8 编码，必须填写已审核通过的签名，可登录 [短信控制台] 查看签名信息 */
        $req->Sign = env('QCLOUD_SMS_SIGN');

        $this->req = $req;
        $this->client = $client;
    }

    public function sendVerifyCode($mobile, $code)
    {
        $this->req->PhoneNumberSet = ["+86{$mobile}"];
        /* 模板 ID: 必须填写已审核通过的模板 ID。可登录 [短信控制台] 查看模板 ID */
        $this->req->TemplateID = env('QCLOUD_SMS_VERIFY_CODE_TEMPLATE_ID');
        /* 模板参数: 若无模板参数，则设置为空*/
        $this->req->TemplateParamSet = ["0" => "{$code}"];
        // 通过 client 对象调用 SendSms 方法发起请求。注意请求方法名与请求对象是对应的
        $resp = $this->client->SendSms($this->req);
        return $resp->toJsonString();
    }

    public function sendSubscribeSms($mobile, $content = [])
    {
        $this->req->PhoneNumberSet = ["+86{$mobile}"];
        /* 模板 ID: 必须填写已审核通过的模板 ID。可登录 [短信控制台] 查看模板 ID */
        $this->req->TemplateID = env('QCLOUD_SMS_SUBSCRIBE_TEMPLATE_ID');
        /* 模板参数: 若无模板参数，则设置为空*/
        $this->req->TemplateParamSet = $content;
        // 通过 client 对象调用 SendSms 方法发起请求。注意请求方法名与请求对象是对应的
        $resp = $this->client->SendSms($this->req);
        return $resp->toJsonString();
    }
}
