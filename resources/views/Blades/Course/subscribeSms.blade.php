<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <link rel="icon" href="https://www.dxever.com/fei/WEB/images/logo.png">
    <link rel="stylesheet" href="css/course/subscribe.css">
    <title>短信订阅</title>
    <script src="js/base/query.js"></script>
    <script src="js/base/config.js"></script>
    <script src="js/base/request.js"></script>
    <script src="js/api/course.js"></script>
</head>
<body>
    <div class="container">
        <div class="icon">
            <img src="image/msgIcon.png" alt="信息">
            订阅短信提醒
        </div>

        @if(empty($user['phone']))
        <div id="unsubscribe">
            <div class="subscribe-line">
                <img src="image/phoneIcon.png" alt="">
                <input type="text" id="phone">
            </div>
            <div class="subscribe-line">
                <img src="image/verifyCodeIcon.png" alt="">
                <input type="text" id="verifycode">
                <div id="sendCode" onclick="sendVerifyCode()">发送验证码</div>
            </div>
            <div class="subscribe-btn-div">
                <span id="subscribe-btn" onclick="subscribe()">订阅</span>
            </div>
        </div>
        <div>
            <input type="checkbox" name="read" id="read">
            <span>已阅读 <span class="rule"> <a href="subscribeRule">[订阅规则]</a> </span></span>
        </div>
        @else
        <div id="subscribe">
            <div class="subscribe">
                已订阅：{{ $user['phone'] }}
            </div>
            <div class="subscribe">
                剩余短信点数：{{ $user['sms_amount'] }}
            </div>
            <div class="subscribe-btn-div">
                <span id="resubscribe-btn" onclick="unsubscribeSms()">取消订阅</span>
            </div>
        </div>
        <div class="pointlogs">
            <div class="tip">点数领取</div>
            <div class="log">
                <div class="log-title">
                    开卡
                </div>
                @if($dxeverData['vipStatus']['status'] == 'ungot')
                <div class="getpoint-btn" onclick="getSmsPoint({{ $dxeverData['vipStatus']['id'] }})">领取</div>
                @elseif($dxeverData['vipStatus']['status'] == 'novip')
                <div class="getpoint-btn" onclick="gotoBuyVip()">未开通vip，点击去开通</div>
                @elseif($dxeverData['vipStatus']['status'] == 'got')
                <div class="getpoint-btn got">已领取</div>
                @endif
            </div>
            @foreach($dxeverData['orders'] as $v)
            <div class="log">
                <div class="log-title">
                    消费
                    <span class="log-title-time">{{ $v['order_create_time'] }}</span>
                </div>
                @if($v['got'])
                <div class="getpoint-btn got">已领取</div>
                @else
                <div class="getpoint-btn" onclick="getSmsPoint({{ $v['id'] }})">领取</div>
                @endif
            </div>
            @endforeach
        </div>
        <div class="pointlogs">
            <div class="tip">短信点数记录(最近50条)</div>
            @foreach($smsAmountLog as $v)
            <div class="log">
                <div class="log-title">
                    @switch($v['from'])
                        @case('openvip')
                        开卡 @break
                        @case('buy')
                        消费 @break
                        @case('sendsms')
                        短信提醒 @break
                    @endswitch
                    <span class="log-title-time">{{ $v['create_time'] }}</span>
                </div>
                @if($v['amount'] > 0)
                <div class="log-num log-inc">+{{ $v['amount'] }}</div>
                @else
                <div class="log-num log-dec">{{ $v['amount'] }}</div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
    <div class="topbanner">
        短信提醒处于预热阶段，短信提醒服务还未开启，但你可以通过印象VIP卡消费来积累短信点数。关于短信提醒和短信点数，请阅读：<a href="subscribeRule">[订阅规则]</a>
    </div>
</body>
<script src="js/course/subscribe.js"></script>
</html>