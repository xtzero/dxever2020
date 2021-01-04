<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <link rel="icon" href="https://www.dxever.com/fei/WEB/images/logo.png">
    <title>设置</title>
    <link rel="stylesheet" href="css/course/course_setting.css">
</head>
<body>
    <div class="container">
        <div class="topempty"></div>
        <div class="item" onclick="jump('subscribeSms')">
            <div class="title">
                <img src="image/msgIcon.png" alt="">
                订阅短信提醒
            </div>
            <div class="right"></div>
        </div>
        <div class="emptyline"></div>
        <div class="item item-vip" onclick="jumpToRace()">
            <div class="title">
                <img src="image/vipIcon.png" alt="">
                「创业合伙人」营销策划实践大赛
            </div>
            <div class="right"></div>
        </div>
        <div class="item item-vip" onclick="jumpToVip()">
            <div class="title">
                <img src="image/vipIcon.png" alt="">
                印象VIP
            </div>
            <div class="right"></div>
        </div>
        <div class="emptyline"></div>
        <div class="item" onclick="jump('feedback')">
            <div class="title">
                <img src="image/feedbackIcon.png" alt="">
                意见反馈
            </div>
            <div class="right"></div>
        </div>
        <div class="item" onclick="jump('about')">
            <div class="title">
                <img src="image/aboutIcon.png" alt="">
                关于大学印象课表
            </div>
            <div class="right"></div>
        </div>
    </div>
    <script>
        function jump(url) {
            window.location.href = url + "?openid={{ $openid }}"
        }
        function jumpToVip() {
            window.location.href = 'http://fanli.dxever.com/tp5/public/index.php/Index/Index/vippage2?aid=2294'
        }
        function jumpToRace() {
            window.location.href = 'https://mp.weixin.qq.com/s/qUo-X1_yauhsBSYw0ia4wQ'
        }
    </script>
</body>
</html>
