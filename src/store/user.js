import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)
import { login } from '@/api/user'

const state = () => ({
    userid: 0,
    access_token: '',
    loginres: {},
    user: {},
    menus: [],
    roles: []
})

const getters = () => ({
    userid: (state) => {
        return state.userid
    },
    access_token: (state) => {
        return state.access_token
    },
    userinfo: (state) => {
        return state.userinfo
    },
})

const mutations = {
    loginSuccess(state, payload) {
        state.userid = payload.user.uid
        state.access_token = payload.token
        state.loginres = payload
        state.user = payload.user
        state.menus = payload.menus
        state.roles = payload.roles
    }
}

const actions = {
    login({commit}, postData) {
        login(postData).then(res => {
            if (res.code == 200) {
                localStorage.setItem('loginRes', JSON.stringify(res.data))
                commit('loginSuccess', res.data)
            } else {
                Vue.prototype.$message.error(res.msg)
            }
        }).catch(e => {
            console.error(e)
            Vue.prototype.$message.error('登录出错啦')
        })
    }
}

export default {
    namespaced: true,
    state: state,
    getters: getters,
    mutations: mutations,
    actions: actions
}