<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <link rel="icon" href="https://www.dxever.com/fei/WEB/images/logo.png">
    <title>反馈</title>
    <link rel="stylesheet" href="css/course/course_feedback.css">
    <script src="js/base/query.js"></script>
    <script src="js/base/config.js"></script>
    <script src="js/base/request.js"></script>
    <script src="js/api/course.js"></script>
    <style>
        #cover {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.5);
            /*display: flex;*/
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 10000;
            transition: all linear 0.5s;
        }
        .cover-alert {
            position: relative;
            width: 80vw;
            min-height: 20vh;
            border-radius: 20px;
            background-color: white;
            transition: all linear 0.2s;
            display: none;
        }
        .cover-alert-title {
            width: 100%;
            text-align: center;
            margin-top: 20px;
            font-size: 18px;
        }
        .cover-alert-content {
            margin: 10px 0px;
            margin-bottom: 60px;
            padding: 0 20px;
        }
        .cover-alert-btn {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 44px;
            line-height: 44px;
            text-align: center;
            /*margin-top: 30px;*/
            margin-bottom: 10px;
        }
        .cover-alert-btn:active {
            background-color: #efeff5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="tip">在这写下你要反馈的内容吧</div>
        <textarea name="content" id="content" cols="30" rows="20" placeholder="我觉得..."></textarea>
        <div class="commit-div">
            <div id="commit">提交</div>
        </div>
    </div>
    <div id="cover">
        <div class="cover-alert" id="cover-alert">
            <div class="cover-alert-title" id="xalert-title">

            </div>
            <div class="cover-alert-content" id="xalert-content">

            </div>
            <div class="cover-alert-btn" id="xalert-btn">好的</div>
        </div>
    </div>
</body>
<script src="js/base/xalert.js"></script>
<script>
    window.onload = function () {
        document.getElementById('commit').onclick = function () {
            var openid = getQueryString('openid')
            var content = document.getElementById('content').value
            if (!content) {
                xalert('请先输入内容', '提示')
                return
            }
            api.course.sendFeedback({
                openid: openid,
                content: content
            }).then(function(res) {
                if (res.code == 200) {
                    xalert('我们会尽快解决你的问题', '提交成功')
                    document.getElementById('content').value = ''
                } else {
                    xalert(res.msg, '出错啦')
                }
            }).catch(function(e) {
                console.error(e)
                xalert('系统错误，请稍后重试', '出错啦')
            })
        }
    }
</script>
</html>