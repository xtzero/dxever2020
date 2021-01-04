import Vue from 'vue'
import Antd from 'ant-design-vue/es'
import App from './App.vue'
import 'ant-design-vue/dist/antd.css'

import router from '@/routers'

import store from '@/store'
Vue.config.productionTip = false
Vue.use(Antd)
new Vue({
  router,
  store,
  render: h => h(App),
}).$mount('#app')