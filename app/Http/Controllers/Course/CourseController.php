<?php


namespace App\Http\Controllers\Course;


use App\Http\Controllers\Base\AppController;
use App\Service\Course\CourseService;
use App\Service\Wx\WxAuthService;
use Illuminate\Http\Request;

class CourseController extends AppController
{
    public $openid;
    public $ifBind;

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function main()
    {
        $openid = $this->request->input('openid');
        if (!$openid) {
            die('请从大学印象跳转登录');
        }
        $ifBind = (new CourseService())->checkBind($openid);
        if (!$ifBind) {
            return redirect("/bindJwAccount?openid=" . $openid);
        }
        return view('Blades.Course.course', [
            'openid' => $openid,
            'studInfo' => $ifBind
        ]);
    }

    public function loginJiaowu()
    {
        $this->param([
            'studno' => ['format' => 'string', 'require' => true],
            'password' => ['format' => 'string', 'require' => true],
            'openid' => ['format' => 'string', 'require' => true]
        ]);

        return (new CourseService())->loginJiaowu($this->params);
    }

    public function curlGetCourse()
    {
        $this->param([
            'openid' => ['format' => 'string', 'require' => true],
            'year' => ['format' => 'string', 'require' => true]
        ]);
        return (new CourseService())->curlGetCourse($this->params);
    }

    public function getCourse()
    {
        $this->param([
            'openid' => ['format' => 'string', 'require' => true],
            'year' => ['format' => 'string', 'require' => true],
            'week' => ['format' => 'string', 'require' => true]
        ]);
        return (new CourseService())->getCourse($this->params);
    }

    public function settings()
    {
        $openid = $this->request->input('openid');
        if (!$openid) {
            die('请从大学印象跳转登录');
        }
        $ifBind = (new CourseService())->checkBind($openid);
        if (!$ifBind) {
            return redirect("/bindJwAccount?openid=" . $openid);
        }

        return view('Blades.Course.setting', [
            'openid' => $openid,
            'studInfo' => $ifBind
        ]);
    }

    public function addSmsAmount()
    {
        $this->param([
            'openid' => ['format' => 'string', 'require' => true],
            'amount' => ['format' => 'int', 'require' => true],
            'from' => ['format' => 'string']
        ]);
        $this->checkSign();
        return (new CourseService())->addSmsAmount($this->params);
    }

    public function feedbackView()
    {
        $openid = $this->request->input('openid');
        if (!$openid) {
            die('请从大学印象跳转登录');
        }
        $ifBind = (new CourseService())->checkBind($openid);
        if (!$ifBind) {
            return redirect("/bindJwAccount?openid=" . $openid);
        }
        return view('Blades.Course.feedback', [
            'user' => $ifBind
        ]);
    }

    /**
     * 反馈
     * @return int
     */
    public function feedback()
    {
        $this->param([
            'openid' => ['format' => 'string', 'require' => true],
            'content' => ['format' => 'string', 'require' => true]
        ]);
        return (new CourseService())->feedback($this->params['openid'], $this->params['content']);
    }

    /**
     * 短信订阅页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function subscribeSmsView()
    {
        $openid = $this->request->input('openid');
        if (!$openid) {
            die('请从大学印象跳转登录');
        }
        $ifBind = (new CourseService())->checkBind($openid);
        if (!$ifBind) {
            return redirect("/bindJwAccount?openid=" . $openid);
        }
        if ($ifBind['phone']) {
            // 获取大学印象的订单和vip
            $syncDxeverData = (new CourseService())->getDxeverOrderListAndSync($openid);
        }
        return view('Blades.Course.subscribeSms', [
            'user' => $ifBind,
            'dxeverData' => $syncDxeverData ?? false,
            'smsAmountLog' => (new CourseService())->getSmsAmountLog($openid)
        ]);
    }

    /**
     * 获取短信点数
     * @return false|string
     */
    public function getSmsAmount()
    {
        $this->param([
            'openid' => ['format' => 'string', 'require' => true],
            'log_id' => ['format' => 'string', 'require' => true]
        ]);

        return (new CourseService())->getSmsAmount($this->params);
    }

    /**
     * 取消订阅
     * @return false|string
     */
    public function unsubscribeSms()
    {
        $this->param([
            'openid' => ['format' => 'string', 'require' => true]
        ]);

        return (new CourseService())->unsubscribeSms($this->params);
    }

    /**
     * 发送验证码
     * @return false|string
     */
    public function subscribeSendVerifycode()
    {
        $this->param([
            'openid' => ['format' => 'string', 'require' => true],
            'phone' => ['format' => 'string', 'require' => true]
        ]);

        return (new CourseService())->subscribeSendVerifycode($this->params);
    }

    /**
     * 订阅短信提醒
     * @return false|string
     */
    public function subscribeSms()
    {
        $this->param([
            'phone' => ['format' => 'string', 'require' => true],
            'code' => ['format' => 'string', 'require' => true],
            'openid' => ['format' => 'string', 'require' => true]
        ]);
        return (new CourseService())->subscribeSms($this->params);
    }

    public function setNomoreVipAd()
    {
        $this->param([
            'openid' => ['format' => 'string', 'require' => true]
        ]);

        return (new CourseService())->setNomoreVipAd($this->params);
    }
}
