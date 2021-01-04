import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter)

const routes = [
    {
        path: '/',
        component: () => import('@/layouts/pageWithMenu'),
        children: [
            {
                path: '/',
                name: '主页',
                component: () => import('@/views/user/home')
            }
        ]
    },
    {
        path: '/auth',
        component: () => import('@/layouts/pageWithMenu'),
        children: [
            {
                path: '/roles',
                name: '角色管理',
                component: () => import('@/views/auth/roles')
            },
            {
                path: '/menus',
                name: '菜单管理',
                component: () => import('@/views/auth/menus')
            },
            {
                path: '/roleMenus',
                name: '角色菜单管理',
                component: () => import('@/views/auth/roleMenus')
            },
            {
                path: '/sysusers',
                name: '系统用户管理',
                component: () => import('@/views/auth/sysUsers')
            }
        ]
    },
    {
        path: '/a',
        redirect: '/',
        component: () => import('@/layouts/pageWithoutMenu'),
        children: [
            {
                path: '/login',
                name: '登录',
                component: () => import('@/views/user/login')
            }
        ]
    }
]

const router = new VueRouter({
    routes
})

router.beforeEach((to, from, next) => {
    console.log(from)
    document.title = to.name ? to.name : ''
    next()
})

export default router
