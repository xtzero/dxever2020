<template>
    <div class="container">
        <div class="topbar">
            <a-page-header :ghost="false" style="border: 1px solid #efeff5">
                <div slot="title">
                    <div class="title">
                        <img alt="logo" class="logo" src="https://www.dxever.com/fei/WEB/images/logo.png" />
                        <span class="title-content">大学印象 后台管理系统 beta 0.1</span>
                    </div>
                </div>
                <template slot="extra">
                    <a-dropdown>
                        <a class="ant-dropdown-link" @click="e => e.preventDefault()">
                            {{ name }}
                            <a-icon type="down" />
                        </a>
                        <a-menu slot="overlay">
                        <a-menu-item>
                            个人中心
                        </a-menu-item>
                        <a-menu-divider />
                        <a-menu-item>
                            <router-link to="/login">退出登录</router-link>
                        </a-menu-item>
                        </a-menu>
                    </a-dropdown>
                </template>
            </a-page-header>
        </div>
        <div class="body">
            <div class="left">
                <div class="menu">
                    <a-menu
                        :default-selected-keys="['1']"
                        :default-open-keys="['1']"
                        mode="inline"
                        theme="dark"
                        :open-keys.sync="openKeys"
                    >
                        <template
                            v-for="(v, k) in menu"
                        >
                            <a-menu-item :key="k" v-if="!v.children">
                                <a-icon :type="v.icon" />
                                <span>
                                    <router-link :to="v.path">
                                        {{ v.name }}
                                    </router-link>
                                </span>
                            </a-menu-item>
                            <a-sub-menu :key="k" v-else>
                                <span slot="title">
                                    <a-icon :type="v.icon" />
                                    <span>
                                        {{ v.name }}
                                    </span>
                                </span>
                                <a-menu-item
                                    v-for="(vv, kk) in v.children"
                                    :key="k + '' + kk"
                                >
                                    <router-link :to="vv.path">
                                        {{ vv.name }}
                                    </router-link>
                                </a-menu-item>
                            </a-sub-menu>
                        </template>
                    </a-menu>
                </div>
            </div>
            <div class="right">
                <div class="appbody">
                    <router-view></router-view>
                </div>
            </div>
        </div>
  </div>
</template>
<script>
import menu from '@/routers/menu'
export default {
    name: 'pageWithMenu',
    data: () => ({
        menu: menu,
        openKeys: []
    }),
    methods: {

    },
    watch: {
        openKeys(val) {
            console.log('openKeys', val);
        }
    },
    computed: {
        name() {
            return this.$store.getters.user.user.name
        }
    }
}
</script>
<style scoped>
.container {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    background-color: #efeff5;
}
.title {
    display: flex;
    flex-direction: row;
    align-items: center;
}
.title-content {
    font-size: 18px;
    font-weight: 500;   
}
.body {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: row;
}
.left {
    width: 256px;
    display: flex;
    flex-direction: column;
}
.logo {
    width: 35px;
    height: 35px;
    background-color: white;
    display: flex;
    flex-direction: row;
    justify-content: space-around;
    align-items: center;
    font-size: 15px;
    color: black;
    margin-right: 20px;
}
.menu {
    height: 100%;
    background-color: #001528;
}
.right {
    width: 100%;
}
.ant-dropdown-link {
    color: black;
}
.appbody {
    width: 100%;
    height: 100%;
    background-color: #efeff5;
    overflow-y: scroll;
}
</style>