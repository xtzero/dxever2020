((function(root){
    root.getSmsPoint = function(logid) {
        api.course.getSmsAmount({
            openid: getQueryString('openid'),
            log_id: logid
        }).then(res => {
            if (res.code == 200) {
                alert('领取成功')
                location.reload()
            } else {
                alert(res.msg)
            }
        }).catch(e => {
            console.error(e)
            alert('出错啦。请稍后重试')
        })
    }

    root.unsubscribeSms = function() {
        if (confirm('确定要取消订阅吗？')) {
            api.course.unsubscribeSms({
                openid: getQueryString('openid')
            }).then(res => {
                if (res.code == 200) {
                    alert('取消订阅成功')
                    location.reload()
                } else {
                    alert(res.msg)
                }
            }).catch(e => {
                console.error(e)
                alert('出错啦。请稍后重试')
            })
        }
    }

    root.gotoBuyVip = function() {
        window.location.href = "http://flzuiduan.cn/tp5/public/index.php/Index/Index/vippage_tz?aid=2294"
    }

    root.sendVerifyCode = function() {
        if (document.getElementById("sendCode").innerHTML == '已发送') {
            alert('如需要重新发送，请刷新页面')
            return
        }
        if (document.getElementById("sendCode").innerHTML == '发送中') {
            return
        }
        var phone = document.getElementById('phone').value
        if (!phone) {
            alert('请输入手机号')
            return
        }
        var openid = getQueryString('openid')
        document.getElementById("sendCode").innerHTML = '发送中'
        api.course.subscribeSendVerifycode({
            openid: openid,
            phone: phone
        }).then(res => {
            if (res.code == 200) {
                document.getElementById("sendCode").innerHTML = '已发送'
            } else {
                alert('发送验证码时出现问题，请稍后重试')
                document.getElementById("sendCode").innerHTML = '发送验证码'
            }
        }).catch(e => {
            console.error(e)
            document.getElementById("sendCode").innerHTML = '发送验证码'
            alert('发送验证码出现问题，请稍后重试')
        })
    }

    root.subscribe = function() {
        var phone = document.getElementById('phone').value
        if (!phone) {
            alert('请输入手机号')
            return
        }
        var code = document.getElementById('verifycode').value
        if (!code) {
            alert('请输入验证码')
            return
        }
        if (!document.getElementById('read').checked) {
            alert('请勾选“已阅读订阅规则”')
            return
        }
        var openid = getQueryString('openid')
        api.course.subscribeSms({
            phone: phone,
            code: code,
            openid: openid
        }).then(res => {
            if (res.code == 200) {
                window.location.reload()
            } else {
                alert('订阅时出现问题:' + res.msg)
            }
        }).catch(e => {
            console.error(e)
            alert('订阅时出现问题，请稍后重试吧')
        })
    }
})(this))