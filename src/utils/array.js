export function objToArr(obj) {
    const arr = []
    for (const i in obj) {
        arr.push(obj[i])
    }
    return arr
}

export function arrToObj(arr) {
    const obj = {}
    for (let i = 0; i < arr.length; i ++) {
        obj[i] = arr[i]
    }
    return obj
}
