<?php


namespace App\Service\Course;


use App\Models\CourseAccountModel;
use App\Models\CourseSmsAmountLogModel;
use App\Models\CourseTableModel;
use App\Models\CourseVisitLogModel;
use App\Models\FeedbackModel;
use App\Service\Base\BaseService;
use App\Service\Wx\QcloudSmsService;
use App\Support\ArraySupport;
use App\Support\CurlSupport;
use App\Support\DateSupport;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\URL;

class CourseService extends BaseService
{
    /**
     * 检查是否绑定
     * @param $openid
     * @return bool|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function checkBind($openid)
    {
        $ifBind = (new CourseAccountModel())->baseSelectFirst([
            ['openid', '=', $openid],
            ['is_del', '=', 0]
        ]);

        if ($ifBind) {
            return $ifBind;
        } else {
            return false;
        }
    }

    public function loginJiaowu($params)
    {
        if (empty($params['studno'])) {
            return self::ajaxError('需要学号');
        }
        if (empty($params['password'])) {
            return self::ajaxError('需要密码');
        }
        if (empty($params['openid'])) {
            return self::ajaxError('需要微信认证');
        }

        $curlLoginToJwc = $this->curlLoginToJwc($params['studno'], $params['password']);
        if (!stripos($curlLoginToJwc['loginRes'], '学生个人中心')) {
            return self::ajaxError('账号错误，或教务处出现问题');
        } else {
            preg_match_all('/<div id="Top1_divLoginName" class="Nsb_top_menu_nc" style="color: #000000;">(.*)<\/div>/', $curlLoginToJwc['loginRes'], $studInfoPreg);
            $studName = substr($studInfoPreg[1][0], 0, strpos($studInfoPreg[1][0], '('));
            $ifExist = (new CourseAccountModel())->baseSelectFirst([
                'openid' => $params['openid'],
                'is_del' => 0
            ]);
            if ($ifExist) {
                $updateAccount = (new CourseAccountModel())->baseUpdate([
                    'openid' => $params['openid']
                ], [
                    'username' => $params['studno'],
                    'password' => $params['password'],
                    'studname' => $studName,
                    'modify_time' => DateSupport::now(),
                    'is_del' => 0
                ]);
            } else {
                $saveAccount = (new CourseAccountModel())->baseInsert([
                    'username' => $params['studno'],
                    'password' => $params['password'],
                    'openid' => $params['openid'],
                    'studname' => $studName,
                    'create_time' => DateSupport::now(),
                    'is_del' => 0
                ]);
            }
            return self::ajax([
                'save' => $saveAccount ?? false,
                'update' => $updateAccount ?? false
            ]);
        }
    }

    public function curlGetCourse($params)
    {
        if (empty($params['openid'])) {
            return self::ajaxError('需要微信认证');
        }
        if (empty($params['year'])) {
            return self::ajaxError('需要指定学年学期');
        }
        $jwAccount = (new CourseAccountModel())->baseSelectFirst([
            'openid' => $params['openid'],
            'is_del' => 0
        ]);
        if (empty($jwAccount)) {
            self::ajaxError('请重新绑定教务处账号', 501);
        }

        $curlLoginToJwc = $this->curlLoginToJwc($jwAccount['username'], $jwAccount['password']);

        if (!stripos($curlLoginToJwc['loginRes'], '学生个人中心')) {
            return self::ajaxError('账号错误，或教务处出现问题', 501);
        } else {
            preg_match_all('/<div id="Top1_divLoginName" class="Nsb_top_menu_nc" style="color: #000000;">(.*)<\/div>/', $curlLoginToJwc['loginRes'], $studInfoPreg);
            $studName = substr($studInfoPreg[1][0], 0, strpos($studInfoPreg[1][0], '('));
            $courseData = $this->curlCourseWebpage($curlLoginToJwc['cookieJar'], $params['year']);
            $save = (new CourseTableModel())->saveCourseData($params['openid'], $courseData, $params['year'], $studName);

            return self::ajax([
                'save' => $save,
                'courseData' => $courseData
            ]);
        }
    }

    public function curlLoginToJwc($username, $password)
    {
        $loginApi = env('JIAOWU_LOGIN_API');
        $loginRes = CurlSupport::post($loginApi, [
            'USERNAME' => $username,
            'PASSWORD' => $password
        ], [], $cookieJar);

        return [
            'loginRes' => $loginRes,
            'cookieJar' => $cookieJar
        ];
    }

    public function curlCourseWebpage($cookieJar, $term)
    {
        $html = CurlSupport::post(env('JIAOWU_COURSE_URL'), [
            'xnxq01id' => $term
        ], [], $cookieJar);

        $html = str_replace(["\n", "\r", "\t", " "], '', $html);
        $tableAStr = '<tableid="kbtable"border="1"';
        $tableA = strpos($html, $tableAStr);
        $tableBStr = '</table></form>';
        $tableB = strpos($html, $tableBStr);
        $table = substr($html, $tableA, $tableB - $tableA);
        $trs = explode('</tr><tr>', $table);
        $tds = [];
        for ($i = 1; $i <= 6; $i ++) {
            $_tds = explode('</td><tdwidth="123"height="28"align="center"valign="top">', $trs[$i]);
            foreach ($_tds as $v) {
                $tds[] = [
                    'v' => $v,
                    'cheapter' => $i
                ];
            }
        }
        $courses = [];
        foreach ($tds as $k => $v) {
            $cheapter = $v['cheapter'];
            $v = $v['v'];
            $cheapterAndDay = explode('-', substr($v, strpos($v, '<inputtype="hidden"name="jx0415zbdiv_2') + 79, 3));
            $day = $cheapterAndDay[0];

            // 一个格子里有多个课的
            if (strpos($v, '---------------------')) {
                preg_match_all('/<divid="(.*)2"style="display:none;"class="kbcontent">(.*)<\/font><br\/><\/div>/', $v, $pregCell);
                $cells = explode('---------------------', $pregCell[2][0]);
                foreach ($cells as &$cell) {
                    $cell = strip_tags(str_replace('<br/>', '|', $cell));
                    $contents = explode('|', $cell);
                    $class = [];
                    $class['name'] = $contents[0];
                    $class['teacher'] = $contents[1];
                    $weeks = explode(',', str_replace('(周)', '', $contents[2]));
                    $weeksArr = [];
                    foreach ($weeks as $week) {
                        if (strpos($week, '-')) {
                            $_week = explode('-', $week);
                            for($i = $_week[0]; $i <= $_week[1]; $i ++) {
                                $weeksArr[] = $i;
                            }
                        } else {
                            $weeksArr[] = (int)$week;
                        }
                    }
                    sort($weeksArr);
                    $class['weeks'] = $weeksArr;
                    $class['classroom'] = $contents[3];
                    $class['cheapter'] = $cheapter;
                    $class['day'] = $day;
                    $courses[] = $class;
                }
            }
            // 一个格子里就一个课的
            else {
                if (strpos($v, 'nputtype="hidden"name=')) {
                    if (strpos($v, 'thwidth="70"height="28"')) {
                        $v = substr($v, strpos($v, '<inputtype="hidden"'));
                    }
                    $cell = strip_tags(str_replace('<br/>', '|', $v));
                    if ($cell != '&nbsp;&nbsp;') {
                        $contents = explode('|', $cell);
                        $class = [];
                        $class['name'] = $contents[0];
                        $class['teacher'] = $contents[4];
                        $weeks = explode(',', str_replace('(周)', '', $contents[1]));
                        $weeksArr = [];
                        foreach ($weeks as $week) {
                            if (strpos($week, '-')) {
                                $_week = explode('-', $week);
                                for($i = $_week[0]; $i <= $_week[1]; $i ++) {
                                    $weeksArr[] = $i;
                                }
                            } else {
                                $weeksArr[] = (int)$week;
                            }
                        }
                        sort($weeksArr);
                        $class['weeks'] = $weeksArr;
                        $class['classroom'] = $contents[2];
                        $class['cheapter'] = $cheapter;
                        $class['day'] = $day;
                        $courses[] = $class;
                    }
                }
            }
        }
        return $courses;
    }

    /**
     * 获取课表数据
     * @param $params
     * @return mixed
     */
    public function getCourse($params)
    {
        $courseData = (new CourseTableModel())->baseSelect([
            'openid' => $params['openid'],
            'year' => $params['year'],
            'week' => $params['week']
        ]);
        $bindinfo = (new CourseAccountModel())->baseSelectFirst([
            'openid' => $params['openid'],
            'is_del' => 0
        ]);
        if (!$bindinfo) {
            return self::ajaxError('你还未绑定，请重新绑定教务处账号');
        }
        $this->saveVisitLog([
            'username' => $bindinfo['username'],
            'openid' => $params['openid'],
            'studname' => $bindinfo['studname'],
            'year' => $params['year'],
            'week' => $params['week']
        ]);
        if (!$courseData) {
            if (!(new CourseTableModel())->baseSelectCount([
                'openid' => $params['openid'],
                'year' => $params['year']
            ])) {
                $this->curlGetCourse([
                    'openid' => $params['openid'],
                    'year' => $params['year']
                ]);
                $courseData = (new CourseTableModel())->baseSelect([
                    'openid' => $params['openid'],
                    'year' => $params['year'],
                    'week' => $params['week']
                ]);
            }
        }
        return self::ajax($courseData);
    }

    public function saveVisitLog($params)
    {
        return (new CourseVisitLogModel())->baseInsert(array_merge($params, [
            'create_time' => DateSupport::now()
        ]));
    }

    /**
     * 增加短信额度
     * @param $params
     * @return array
     */
    public function addSmsAmount($params)
    {
        $addAmount = (new CourseAccountModel())->baseInc([
            'openid' => $params['openid']
        ],'sms_amount', $params['amount'] ?? 1);
        $addLog = (new CourseSmsAmountLogModel())->baseInsert([
            'openid' => $params['openid'],
            'amount' => $params['amount'],
            'create_time' => DateSupport::now(),
            'from' => $params['from'] ?? ''
        ]);
        return [
            'addAmount' => $addAmount,
            'addLog' => $addLog
        ];
    }

    /**
     * 反馈
     * @param $openid
     * @param $content
     * @return int
     */
    public function feedback($openid, $content)
    {
        $insert = (new FeedbackModel())->baseInsert([
            'openid' => $openid,
            'content' => $content,
            'create_time' => DateSupport::now()
        ]);
        return self::ajax([
            'insert' => $insert
        ]);
    }

    public function getDxeverOrderListAndSync($openid)
    {
        // 取大学印象消费记录
        $dxeverOrderRes = json_decode(file_get_contents(env('DXEVER_ORDER_API') . $openid), true);
        // 取出来已保存的所有的订单
        $savedOrder = (new CourseSmsAmountLogModel())->baseSelect([
            'openid' => $openid,
            'from' => 'buy'
        ], ['pid']);
        $savedOrderPids = array_column($savedOrder, 'pid');
        $insertLogData = [];    // 准备保存的订单
        if ($dxeverOrderRes['meta']['code'] == 200) {
            $dxeverOrders = $dxeverOrderRes['data'];
            foreach ($dxeverOrders as $k => &$v) {
                // 去掉十块以下的订单
                if ((int)$v['amount'] < 1000) {
                    continue;
                }
                // 如果今天还未保存这个订单，保存上
                if (!in_array($v['pid'], $savedOrderPids)) {
                    $insertLogData[] = [
                        'openid' => $openid,
                        'pid' => $v['pid'],
                        'amount' => 1,
                        'order_create_time' => $v['timestamp'],
                        'create_time' => DateSupport::now(),
                        'from' => 'buy',
                        'got' => 0
                    ];
                }
            }
            // 把没保存的存上
            if (!empty($insertLogData)) {
                foreach ($insertLogData as $v) {
                    (new CourseSmsAmountLogModel())->baseInsert($v);
                }
            }
        }

        // 订单记录 领取和未领取的都拿出来
        $savedOrder = (new CourseSmsAmountLogModel())->baseSelect([
            'openid' => $openid,
            'from' => 'buy',
            'got' => 0
        ], ['id', 'pid', 'amount', 'order_create_time', 'got']);

        // 看看这个人开没开卡
        $dxeverVipRes = json_decode(file_get_contents(env('DXEVER_VIP_API') . $openid), true);
        if ($dxeverVipRes['meta']['code'] == 200 && $dxeverVipRes['data']['vip'] == 1) {
            // 看看我存没存
            $vipLog = (new CourseSmsAmountLogModel())->baseSelectFirst([
                'openid' => $openid,
                'from' => 'openvip'
            ]);
            if ($vipLog) {
                $vipStatus = $vipLog['got'] ? 'got' : 'ungot';
            } else {
                $vipStatus = 'ungot';
                (new CourseSmsAmountLogModel())->baseInsert([
                    'openid' => $openid,
                    'pid' => '',
                    'amount' => 10,
                    'order_create_time' => $dxeverVipRes['data']['timestamp'],
                    'create_time' => DateSupport::now(),
                    'from' => 'openvip',
                    'got' => 0
                ]);
            }
        } else {
            $vipStatus = 'novip';
        }

        // 拿出来vip的记录
        $vipLog = (new CourseSmsAmountLogModel())->baseSelectFirst([
            'openid' => $openid,
            'from' => 'openvip'
        ]) ?? [];
        return [
            'orders' => $savedOrder,
            'vipStatus' => array_merge($vipLog, [
                'status' => $vipStatus
            ])
        ];
    }

    /**
     * 获取短信额度获取记录
     * @param $openid
     * @return mixed
     */
    public function getSmsAmountLog($openid)
    {
        return ArraySupport::stdClassToArray((new CourseSmsAmountLogModel())->baseQuery([
            'openid' => $openid,
            'got' => 1
        ])->limit(50)->get());
    }

    /**
     * 获取短信点数
     * @param $params
     * @return false|string
     */
    public function getSmsAmount($params)
    {
        $log = (new CourseSmsAmountLogModel())->baseSelectFirst([
            'id' => $params['log_id'],
            'openid' => $params['openid']
        ]);
        if (!$log) {
            return self::ajaxError('这条记录不存在');
        }
        if ($log['amount'] <= 0) {
            return self::ajaxError('这条记录不能领取短信点数');
        }

        DB::beginTransaction();
        try {
            $got = (new CourseSmsAmountLogModel())->baseUpdate([
                'id' => $params['log_id']
            ], [
                'got' => 1
            ]);
            $inc = (new CourseAccountModel())->baseInc([
                'openid' => $params['openid']
            ], 'sms_amount', $log['amount']);

            DB::commit();
            return self::ajax([
                'got' => $got,
                'inc' => $inc
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return self::ajaxError('系统错误，请稍后重试');
        }
    }

    /**
     * 取消订阅
     * @param $params
     * @return false|string
     */
    public function unsubscribeSms($params)
    {
        $update = (new CourseAccountModel())->baseUpdate([
            'openid' => $params['openid']
        ], [
            'phone' => '',
            'modify_time' => DateSupport::now()
        ]);
        return self::ajax([
            'update' => $update
        ]);
    }

    public function subscribeSendVerifycode($params)
    {
        if (empty($params['phone'])) {
            return self::ajaxError('请输入手机号');
        }
        if (empty($params['openid'])) {
            return self::ajaxError('请登录');
        }
        $redisKeyUnique = 'sms_verify_code_unique_' . $params['phone'];
        $ifSendJustNow = Redis::get($redisKeyUnique);
        if ($ifSendJustNow) {
            return self::ajaxError('刚刚发送过，请等待' . ($ifSendJustNow + 90 - time()). '秒后重试');
        }
        $now = time();
        Redis::setex($redisKeyUnique, 90, $now);
        $code = rand(1000, 9999);

        $redisKeyVerifyCode = 'sms_verify_code_' . $params['phone'] . "_" . $params['openid'];
        Redis::setex($redisKeyVerifyCode, 3600, $code);
        $send = (new QcloudSmsService())->sendVerifyCode($params['phone'], $code);
        return self::ajax([
            'send' => $send,
            'unique' => $now + 90,
            'exp' => $now + 3600
        ]);
    }

    /**
     * 订阅短信提醒
     * @param $params
     * @return false|string
     */
    public function subscribeSms($params)
    {
        if (empty($params['phone'])) {
            return self::ajaxError('缺少手机号');
        }
        if (empty($params['code'])) {
            return self::ajaxError('请输入验证码');
        }
        if (empty($params['openid'])) {
            return self::ajaxError('请登录');
        }

        $redisKeyVerifyCode = 'sms_verify_code_' . $params['phone'] . "_" . $params['openid'];
        $code = Redis::get($redisKeyVerifyCode);
        if ($code && $code == $params['code']) {
            $update = (new CourseAccountModel())->baseUpdate([
                'openid' => $params['openid']
            ], [
                'phone' => $params['phone']
            ]);
            return self::ajax([
                'update' => $update
            ]);
        } else {
            Redis::del($redisKeyVerifyCode);
            return self::ajaxError('验证码不正确，请重新获取');
        }
    }

    public function setNomoreVipAd($params)
    {
        if (empty($params['openid'])) {
            return self::ajaxError('请登录');
        }

        $update = (new CourseAccountModel())->baseUpdate([
            'openid' => $params['openid']
        ], [
            'nomore_vip_ad' => 1
        ]);

        return self::ajax([
            'update' => $update
        ]);
    }
}
