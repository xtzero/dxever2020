import whitelist from './whiteList'
import env from '../../env'

const config = {
    ...env, whitelist: whitelist
}

export default function getConf(key) {
    return config[key]
}

