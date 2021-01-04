export function formatDate(second) {
    let day = 0
    let hour = 0
    let minute = 0
    if (second >= 60) {
        minute = parseInt(second / 60)
        second = parseInt(second % 60)
        if (minute >= 60) {
            hour = parseInt(minute / 60)
            minute = parseInt(minute % 60)
            if (hour >= 24) {
                day = parseInt(hour / 24)
                hour = parseInt(hour % 24)
            }
        }
    }

    let result = ''
    if (day > 0) {
        result += day + '天 '
    }
    if (hour > 0) {
        result += hour + '时 '
    }
    if (minute > 0) {
        result += minute + '分 '
    }
    if (result === '' || second > 0) {
        result += second + '秒 '
    }
    return result
}

export function date(dateObj, format) {
    if (typeof dateObj == 'string') {
        dateObj = dateObj.replace(/-/ig, '/',)
    }
    const date = new Date(dateObj)
    const Y = date.getFullYear()
    let m = date.getMonth() + 1
    if (m < 10) {
        m = '0' + m
    }
    let d = date.getDate()
    if (d < 10) {
        d = '0' + d
    }
    let H = date.getHours()
    if (H < 10) {
        H = '0' + H
    }
    let i = date.getMinutes()
    if (i < 10) {
        i = '0' + i
    }
    let s = date.getSeconds()
    if (s < 10) {
        s = '0' + s
    }
    return format.replace('Y', Y)
                .replace('m', m)
                .replace('d', d)
                .replace('H', H)
                .replace('i', i)
                .replace('s', s)
}