import Vue from 'vue'
import VueRouter, { RouteConfig } from 'vue-router'

Vue.use(VueRouter)

  const routes: Array<RouteConfig> = [
  {
    path: '/',
    name: '/',
    redirect: '/WithTabLayout'
  },
  {
    path: '/FullscreenLayout',
    name: 'FullscreenLayout',
    component: () => import('../layouts/Fullscreen.vue'),
    children: [
      {
        path: '/login',
        name: '登录',
        component: () => import('../views/Login.vue')
      },
      {
        path: '/userList',
        name: '用户数据',
        component: () => import('../views/about/UserList.vue')
      },
      {
        path: '/orderList',
        name: '订单数据',
        component: () => import('../views/about/OrderList.vue')
      },
      {
        path: '/dailyStatistic',
        name: '每日统计',
        component: () => import('../views/about/DailyStatistic.vue')
      }
    ]
  },
  {
    path: '/WithTabLayout',
    name: 'WithTabLayout',
    component: () => import('../layouts/Withtab.vue'),
    children: [
      {
        path: '/home',
        name: '我',
        component: () => import('../views/Home.vue')
      },
      {
        path: '/about',
        name: '数据',
        component: () => import('../views/About.vue')
      }
    ]
  }
]

const router = new VueRouter({
  routes
})

router.beforeEach((to, from, next) => {
  document.title = to.name ?? ''
  next();
});

export default router
