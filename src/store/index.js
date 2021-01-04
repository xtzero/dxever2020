import Vue from 'vue'
import Vuex from 'vuex'
import C from '@/config'

import user from './user'

Vue.use(Vuex)

export default new Vuex.Store({
    modules: {
        user: user
    },
    strict: C('debug'),
    getters: {
        user: state => state.user
    }
})