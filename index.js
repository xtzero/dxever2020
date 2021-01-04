(function(root) {
    document.getElementById('ok').onclick = function() {
        var formdata = new FormData()
        var files = $('#file').get(0).files[0]
        if (files) {
            formdata.append('files', $('#file').get(0).files[0])
        } else {
            console.log('没有文件')
            return
        }
        
        $('#status').html('开始上传')
        $.ajax({
            url: 'http://localhost:1111/index.php',
            type: "POST",
            processData: false,
            contentType: false,
            data: formdata,
            success: function(res) {
                res = JSON.parse(res)
                var url = 'http://qiniu.pet.xtzero.me/' + res.key
                $('#img').attr('src', url);
                $('#status').html('上传成功，图片网址是：' + url)
            },
            error: function(e) {
                console.log(e)
                $('#status').html('上传失败，请查看控制台')
                alert('上传失败，请刷新重试')
            }
        })
    }
})(this)