import c from '@/config'
import router from '@/routers'
import md5 from '@/utils/md5'
import rand from '@/utils/math'
import store from '@/store'
import axios from 'axios'

const whitelist = c('whitelist').notLogin
const genSign = (data) => {
    const signKey = c('signKey')
    return md5(md5(Object.keys(data).sort().map(k => {
        const v = data[k]
        return v ? v : 'empty-value'
    }).join(',') + signKey) + rand(1, 100))
}

export function request(method, url, data, config = {}) {
    // 处理token和登录
    let token = ''
    if (!whitelist.includes(url)) {
        token = store.getters.user.access_token
        if (token === '') {
            router.push('login')
        } else {
            config.headers = {}
            config.headers['access-token'] = token
        }
    }
    // 处理签名
    const sign = genSign(data)
    data.sign = sign

    let option = {
        method: method,
        url: c('apiHost') + url,
        headers: { ...config.headers }
    }
    const getOption = {
        params: {...data},
    }
    const postOption = {
        data: data
    }

    if (method === 'GET') { 
        option = {...option, ...getOption}
    } else if (method === 'POST') { 
        option = {...option, ...postOption}
    }

    return new Promise((resolve, reject) => {
        axios(option).then(
            value => { 
                // value.data 是纯数据
                if (value.data.code == 50002) {
                    router.push('/login')
                } else {
                    resolve(value.data)
                }
            },
            reason => { 
                reject(new Error(reason))
            }
        )
    })
    
}

// function request_old(method, url, data, config = {}) {
//     // 处理token和登录
//     let token = ''
//     if (!whitelist.includes(url)) {
//         token = store.getters.user.access_token
//         if (token === '') {
//             router.push('login')
//         } else {
//             config.headers = {}
//             config.headers['access-token'] = token
//         }
//     }
//     // 处理签名
//     const sign = genSign(data)
//     data.sign = sign

//     return new Promise((resolve, reject) => {
//         const xhr = new XMLHttpRequest()
//         const fm = new FormData()
//         url = c('apiHost') + url
//         if (Object.keys(data).length > 0) {
//             if (method == 'GET') {
//                 const paramsArr = []
//                 Object.keys(data).forEach(k => {
//                     if (data[k] != undefined) paramsArr.push(`${k}=${data[k]}`)
//                 })
//                 url += '?' + paramsArr.join('&')
//             } else if(method == 'POST') {
//                 Object.keys(data).forEach(k => {
//                     if (data[k] != undefined) fm.append(k, data[k])
//                 })
//             }
//         }

//         xhr.open(method, url, true)
//         xhr.onreadystatechange = () => {
//             if (xhr.readyState == 4){
//                 if (xhr.status == 200 || xhr.status == 304){
//                     const resData = JSON.parse(xhr.responseText)
//                     if (resData.code == 50002) {
//                         router.push('login')
//                     } else {
//                         resolve(resData, xhr)
//                     }
//                 } else {
//                     reject(xhr)
//                 }
//             }
//         }

//         if (config.headers) {
//             Object.keys(config.headers).forEach(k => {
//                 xhr.setRequestHeader(k,config.headers[k])
//             })
//         }

//         if (method == 'GET') {
//             xhr.send()
//         } else if (method == 'POST') {
//             xhr.send(fm)
//         }
//     })
// }
