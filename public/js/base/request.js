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
                // url = config.apiHost + url + '?' + paramArr.join('&')
                url = url + '?' + paramArr.join('&')
            }
            xhr.open('GET', url, true)
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4) {
                    if (xhr.status == 200 || xhr.status == 304) {
                        resolve(JSON.parse(xhr.responseText))
                    } else {
                        reject(xhr)
                    }
                }
            }
            xhr.send()
        })
    }

    root.post = function(url, data, options) {
        return new Promise(function(resolve, reject) {
            var xhr = new XMLHttpRequest()
            // url = config.apiHost + url
            xhr.open('POST', url, true)
            xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded')
            if (options && options.headers && typeof options.headers == 'object' && typeof options.headers == 'undefined') {
                Object.keys(options.headers).forEach(function(k) {
                    var v = options.headers[k]
                    xhr.setRequestHeader(k, v)
                })
            }
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4) {
                    if (xhr.status == 200 || xhr.status == 304) {
                        resolve(JSON.parse(xhr.responseText))
                    } else {
                        reject(xhr)
                    }
                }
            }

            var fm = []
            if (data && typeof data == 'object' && typeof data.length == 'undefined') {
                Object.keys(data).forEach(function(k) {
                    var v = data[k]
                    fm.push(`${k}=${v}`)
                })
            }
            
            xhr.send(fm.join('&'))
        })
    }
})(this)