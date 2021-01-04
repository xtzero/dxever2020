import { get } from '@/utils/request'

export function ifBusinessExist(data) {
    return get('ifBusinessExist', data)
}
export function baseInfo(data) {
    return get('baseInfo', data)
}
export function userList(data) {
    return get('userList', data)
}
export function orderList(data) {
    return get('orderList', data)
}
export function dailyStatistic(data) {
    return get('dailyStatistic', data)
}