import { request } from '@/utils/request'

export function login(data) {
    return request('POST', 'account/login', data)
}

export function homepage(data) {
    return request("GET", 'homepage', data)
}