(function(root){
    root.get = function(url, data) {
        return new Promise(function(resolve, reject) {
            var xhr = new XMLHttpRequest()
            if (typeof data == 'object' && typeof data.length == 'undefined') {
                var paramArr = []
                Object.keys(data).forEach(function(k) {
                    var v = data[k]
                    paramArr.push(`${k}=${v}`)
                })
                url = url + '?' + paramArr.join('&')
            }
            xhr.open('GET', url, false)
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4) {
                    if (xhr.status == 200 || xhr.status == 304) {
                        resolve(xhr.responseText)
                    } else {
                        reject(xhr)
                    }
                }
            }
            xhr.send()
        })
    }
})(this)