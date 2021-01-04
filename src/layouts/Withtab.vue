<template>
    <div class="withtab-container">
        <router-view></router-view>
        <van-tabbar v-model="tabIndex" placeholder>
            <van-tabbar-item :icon="v.icon" v-for="(v,k) in tabRoute" :key="k" replace :to="v.route">
                {{ v.title }}
            </van-tabbar-item>
        </van-tabbar>
    </div>
</template>
<script>
import config from '@/utils/config'
export default {
    data() {
        return {
            tabIndex: 0,
            tabRoute: [
                {
                    title: '我',
                    icon: 'diamond-o',
                    route: '/home'
                },
                {
                    title: '数据',
                    icon: 'bar-chart-o',
                    route: '/about'
                }
            ]
        }
    },
    created() {
        this.checkToken()
        this.initTabActive()
    },
    methods: {
        checkToken() {
            const bid = localStorage.getItem(config.bidKey)

            if (!bid) {
                this.$toast('请使用微信扫码登录')
                setTimeout(() => {
                    this.$router.push('/login')
                }, 1000);
            }
        },
        initTabActive() {
            if (this.$route.path !== this.tabRoute[0].route) {
                this.$router.push(this.tabRoute[0].route)
            }
        }
    }
}
</script>
<style scoped>
    .withtab-container {
        display: flex;
        flex-direction: column;
    }
</style>