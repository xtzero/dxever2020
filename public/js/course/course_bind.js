(function(root){
    var openid = getQueryString('openid')
    if (!openid) {
        alert('请从大学印象跳转登录')
        document.getElementsByTagName('body')[0].innerText = '请从大学印象跳转登录'
        return
    }
    document.getElementById('commit').onclick = function(e) {
        var studno = document.getElementById('studno').value
        var password = document.getElementById('password').value
        if (!studno) {
            alert('请输入教务处学号')
            return
        }
        if (!password) {
            alert('请输入教务处密码')
            return
        }

        api.course.loginJiaowu({
            studno: studno,
            password: password,
            openid: openid
        }).then(function(res) {
            console.log(res)
            if (res.code == 200) {
                window.location.href = `/?openid=${openid}`
            } else {
                alert(res.msg)
            }
        })
    }
})(this)
