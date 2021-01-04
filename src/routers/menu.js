export default [
    {
        path: '/',
        icon: 'mail',
        name: '控制台'
    },
    {
        path: '/base',
        icon: 'mail',
        name: '系统设置',
        children: [
            {
                path: '/roles',
                icon: 'mail',
                name: '角色管理'
            },
            {
                path: '/menus',
                icon: 'mail',
                name: '菜单管理'
            },
            {
                path: '/sysusers',
                icon: 'mail',
                name: '系统用户管理'
            }
        ]
    },
    {
        path: '/statistic',
        icon: 'mail',
        name: '返利卡管理',
        children: [
            {
                path: '/shops',
                icon: 'mail',
                name: '商家管理'
            },
            {
                path: '/users',
                icon: 'mail',
                name: '用户管理'
            },
            {
                path: '/summary',
                icon: 'mail',
                name: '总体数据'
            },
            {
                path: '/shopSallary',
                icon: 'mail',
                name: '商家流水'
            },
            {
                path: '/userOrders',
                icon: 'mail',
                name: '用户消费记录'
            }
        ]
    },
    {
        path: '/course',
        icon: 'mail',
        name: '课表管理',
        children: [
            {
                path: '/course/summary',
                icon: 'mail',
                name: '总体数据'
            },
            {
                path: '/course/jwAccount',
                icon: 'mail',
                name: '教务处账号'
            },
            {
                path: '/course/feedback',
                icon: 'mail',
                name: '用户反馈'
            }
        ]
    }
]