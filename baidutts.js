(function(root) {
    var phpHost = 'http://localhost:1111';
    root.play = function(text) {
        document.getElementById('status').innerHTML = '正在发送'
        setTimeout(() => {
            get(`${phpHost}/index.php`, {
                text: text
            }).then(res => {
                document.getElementById('status').innerHTML = '发送完了'
                document.getElementById('aud').src = phpHost + '/' + res
                document.getElementById('aud').play()
            })   
        }, 100);
    }
    document.getElementById('ok').onclick = function() {
        var text = document.getElementById('text').value
        if (!text) {
            alert('你输入点啥，要不我干张嘴不出声啊')
            return
        }
        play(text)
    }
})(this)