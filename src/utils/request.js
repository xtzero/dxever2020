import axios from 'axios'
import config from '@/utils/config'
import getRandomNum from './math.js'
import md5 from 'js-md5'
import router from "../router"

function genSign(params) {
    const SignatureKey = config.signKey
    const paramsSort = Object.keys(params).sort()
    const paramsArr = []
    for (let i = 0; i < paramsSort.length; i ++) {
        const k = paramsSort[i]
        if (params[k]) {
            paramsArr.push(params[k])
        }
    }
    const sign = md5(md5(paramsArr.join(',') + SignatureKey) + getRandomNum(1, 100))
    return sign
}

function readyData(data) {
    data.sign = genSign(data)
    return data
}

export function post(url, data = {}) {
    const axiosConfig = {}
    const bid = localStorage.getItem(config.bidKey)
    if (!bid) {
        router.push('/login')
    }
    data = readyData(data)
    return new Promise((resolve, reject) => {
        return axios.post(config.baseApiUrl + url, data, axiosConfig).then((res) => {
            if (res.status === 200) {
                if (res.data.code === 50002) {
                    router.push('/login')
                } else {
                    resolve(res.data)
                }
            } else {
                reject({
                    code: 600001,
                    msg: 'axios return error',
                    data: {}
                })
            }
        })
    })
}

export function get(url, data = {}) {
    const bid = localStorage.getItem(config.bidKey)
    if (!bid) {
        router.push('/login')
    }
    data = readyData(data)
    return new Promise((resolve, reject) => {
        return axios.get(config.baseApiUrl + url, {
            params: data
        }).then((res) => {
            if (res.status === 200) {
                if (res.data.code === 50002) {
                    router.push('/login')
                } else {
                    resolve(res.data)
                }
            } else {
                reject({
                    code: 600001,
                    msg: 'axios return error',
                    data: {}
                })
            }
        })
    })
}
