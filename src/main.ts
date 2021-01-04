import Vue from 'vue'
import App from './App.vue'
import router from './router'
import Vant from 'vant';
import 'vant/lib/index.css';
import {
  Toast,
  Dialog,
  Notify,
  ImagePreview
} from 'vant'
Vue.use(Vant)
Vue.config.productionTip = false
Vue.prototype.$toast = Toast
Vue.prototype.$dialog = Dialog
Vue.prototype.$notify = Notify
Vue.prototype.$confirm = Dialog.confirm
Vue.prototype.$alert = Dialog.alert
Vue.prototype.$imagePreview = ImagePreview
new Vue({
  router,
  render: h => h(App)
}).$mount('#app')
