<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <link rel="icon" href="https://www.dxever.com/fei/WEB/images/logo.png">
    <title>{{ $studInfo['studname'] . '的' }}课表</title>
    <link rel="stylesheet" href="css/course/course.css">
    <script src="js/base/query.js"></script>
    <script src="js/base/config.js"></script>
    <script src="js/base/request.js"></script>
    <script src="js/api/course.js"></script>
</head>
<body>
    <div id="bg">
        <!--顶部工具栏-->
        <div id="toolbar">
            <div class="userinfo">
                <div class="studno">{{ $studInfo['username'] }}</div>
                <div class="studname">
                    {{ $studInfo['studname'] }}
                    <a href="/bindJwAccount?openid={{ $openid }}">换绑</a>
                </div>
            </div>
            <div class="timeinfo">
                <div>
                    <select name="" id="terms">
                        <option value="2020-1 ~ 2020-2">学期</option>
                    </select>
                </div>
                <div>
                    <select name="" id="weeks">
                        <option value="0">加载周数...</option>
                    </select>
                </div>
            </div>
        </div>
        <!--空格-->
        <div id="emptyCell">
            <img src="image/settingIcon.png" alt="设置" width="20">
        </div>
        <!--周几那一排-->
        <div id="days">
            <div class="days">
                <span class="days-day">周一</span>
                <span class="days-date">...</span>
            </div>
            <div class="days">
                <span class="days-day">周二</span>
                <span class="days-date">...</span>
            </div>
            <div class="days">
                <span class="days-day">周三</span>
                <span class="days-date">...</span>
            </div>
            <div class="days">
                <span class="days-day">周四</span>
                <span class="days-date">...</span>
            </div>
            <div class="days">
                <span class="days-day">周五</span>
                <span class="days-date">...</span>
            </div>
            <div class="days">
                <span class="days-day">周六</span>
                <span class="days-date">...</span>
            </div>
            <div class="days">
                <span class="days-day">周日</span>
                <span class="days-date">...</span>
            </div>
        </div>
        <!--第几节那一列-->
        <div id="times">
            <table class="times" id="times1"><tr><td>
                <span class="classnum">1-2</span> <br>
                <div class="classtime">8:00</div>
                <div class="classtime">9:40</div>
            </td></tr></table>
            <table class="times" id="times2"><tr><td>
                <span class="classnum">3-4</span> <br>
                <div class="classtime">10:05</div>
                <div class="classtime">11:45</div>
            </td></tr></table>
            <table class="times" id="times3"><tr><td>
                <span class="classnum">5-6</span> <br>
                <div class="classtime">13:20</div>
                <div class="classtime">14:55</div>
            </td></tr></table>
            <table class="times" id="times4"><tr><td>
                <span class="classnum">7-8</span> <br>
                <div class="classtime">15:15</div>
                <div class="classtime">16:50</div>
            </td></tr></table>
            <table class="times" id="times5"><tr><td>
                <span class="classnum-small">9-10</span> <br>
                <div class="classtime">18:00</div>
                <div class="classtime">19:40</div>
            </td></tr></table>
            <table class="times" id="times6"><tr><td>
                <span class="classnum-small">11-12</span> <br>
            </td></tr></table>
        </div>
        <!--课表主区域-->
        <div id="maintable">
            <!-- <table class="maintable-cell"><tr><td>
                    <div class="maintable-cell-classname">工程制图</div>
                    <div class="maintable-cell-classroom">@综A311</div>
                    <div class="maintable-cell-teachername">帐篷（）</div>
            </td></tr></table>
            <table class="maintable-cell"><tr><td>
                    <div class="maintable-cell-classname">体育4</div>
                    <div class="maintable-cell-classroom">@篮球馆</div>
                    <div class="maintable-cell-teachername">邢蕾（讲师）</div>
            </td></tr></table>
            <table class="maintable-cell"><tr><td>
                    <div class="maintable-cell-classname">食品工程原理1</div>
                    <div class="maintable-cell-classroom">@综A311</div>
                    <div class="maintable-cell-teachername">张雪松（讲师）</div>
            </td></tr></table>
            <table class="maintable-cell"><tr><td>
                    <div class="maintable-cell-classname"></div>
                    <div class="maintable-cell-classroom"></div>
                    <div class="maintable-cell-teachername"></div>
            </td></tr></table>
            <table class="maintable-cell"><tr><td>
                    <div class="maintable-cell-classname">电工电子技术</div>
                    <div class="maintable-cell-classroom">@综A311</div>
                    <div class="maintable-cell-teachername">牟俊（讲师）</div>
            </td></tr></table>
            <table class="maintable-cell"><tr><td>
                    <div class="maintable-cell-classname">工程制图</div>
                    <div class="maintable-cell-classroom">@综A311</div>
                    <div class="maintable-cell-teachername">帐篷（）</div>
            </td></tr></table>
            <table class="maintable-cell"><tr><td>
                    <div class="maintable-cell-classname">体育4</div>
                    <div class="maintable-cell-classroom">@篮球馆</div>
                    <div class="maintable-cell-teachername">邢蕾（讲师）</div>
            </td></tr></table>
            <table class="maintable-cell"><tr><td>
                    <div class="maintable-cell-classname">食品工程原理1</div>
                    <div class="maintable-cell-classroom">@综A311</div>
                    <div class="maintable-cell-teachername">张雪松（讲师）</div>
            </td></tr></table>
            <table class="maintable-cell"><tr><td>
                    <div class="maintable-cell-classname"></div>
                    <div class="maintable-cell-classroom"></div>
                    <div class="maintable-cell-teachername"></div>
            </td></tr></table>
            <table class="maintable-cell"><tr><td>
                    <div class="maintable-cell-classname">电工电子技术</div>
                    <div class="maintable-cell-classroom">@综A311</div>
                    <div class="maintable-cell-teachername">牟俊（讲师）</div>
            </td></tr></table> -->
        </div>
        <div id="menuBtn">
            <img src="image/refreshIcon.png" alt="刷新" width="20" class="turnaround">
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
    </div>
</body>
<script src="js/base/xalert.js"></script>
<script src="js/course/course.js"></script>
@if($studInfo['nomore_vip_ad'] == 0)
<script>
// 弹出广告
xalert('印象杯「创业合伙人」营销策划实践大赛火热报名中。参加比赛可获得创新学分，更有两万奖金等你拿！<br/><a href="https://mp.weixin.qq.com/s/qUo-X1_yauhsBSYw0ia4wQ">👉 了解详情请戳我</a>', 'AD', () => {
    api.course.setNomoreVipAd({openid: "{{ $studInfo['openid'] }}"})
    xalert('大赛入口放在左上角齿轮中，可点击查看', 'AD')
})
</script>
@endif
</html>
