<template>
    <div class="container">
        <div class="hello">
            <span class="welcome">欢迎回来，</span> <br>
            <span class="name">{{ name }}，</span> <br>
            <span class="hello-text">{{ genHelloText() }}</span>
        </div>
        <div class="data">
            <span class="welcome">今日数据</span> <br>
            <span class="data-text"> 共计{{ data.vip1 }}个会员, {{ data.vip2 }}个月卡, {{ data.vip3 }}个试用会员, {{ data.vip0 }}个人点进来但还没开卡。</span> <br>
            <span class="data-text"> 新增{{ data.vip1Inc }}个会员, {{ data.vip2Inc }}个月卡, {{ data.vip3Inc }}个试用会员, {{ data.vip0Inc }}个用户。</span>
        </div>
    </div>
</template>
<script>
import { homepage } from '@/api/user'
export default {
    name: 'home',
    data() {
        return {
            loading: false,
            data: {
                vip0: 0,
                vip1: 0,
                vip2: 0,
                vip3: 0,
                vip0Inc: 0,
                vip1Inc: 0,
                vip2Inc: 0,
                vip3Inc: 0
            },
            helloText: ''
        }
    },
    mounted() {
        this.getHomepageData()
    },
    methods: {
        genHelloText() {
            const d = new Date()
            const hour = d.getHours()
            if (hour >= 4 && hour < 9) {
                return '早上好~'
            } else if (hour >= 9 && hour < 11) {
                return '上午好~'
            } else if (hour >= 11 && hour < 13) {
                return '午安~'
            } else if (hour >= 13 && hour < 17) {
                return '下午好~'
            } else if (hour >= 17 && hour < 19) {
                return '吃了没？'
            } else if (hour >= 19 && hour < 23) {
                return '晚上好~'
            } else if (hour >= 23 || hour < 4) {
                return '夜深了。'
            } else {
                return '你电脑的表好像坏了？'
            }
        },
        getHomepageData() {
            this.loading = true
            homepage({}).then(res => {
                if (res.code == 200) {
                    res.data.userCount.forEach(v => {
                        switch(v.vip) {
                            case 0: this.data.vip0 = v.c; break
                            case 1: this.data.vip1 = v.c; break
                            case 2: this.data.vip2 = v.c; break
                            case 3: this.data.vip3 = v.c; break
                        }
                    })
                    this.data.vip0Inc = res.data.userInc.vip0
                    this.data.vip1Inc = res.data.userInc.vip1
                    this.data.vip2Inc = res.data.userInc.vip2
                    this.data.vip3Inc = res.data.userInc.vip3
                } else {
                    this.$message.error(res.msg)
                }
            }).catch(e => {
                console.error(e)
                this.$message.error('获取用户数据时出现问题')
            }).finally(() => {
                this.loading = false
            })
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
.hello {
    width: 100%;
    background-color: #fff;
    padding: 60px 20px;
    font-size: 20px;
    font-weight: 500;
}
.name {
    color: #3498DB;
    font-size: 50px;
}
.hello-text {
    font-size: 35px;
}
.data {
    width: 100%;
    background-color: #fff;
    padding: 60px 20px;
    font-size: 35px;
    font-weight: 500;
    margin-top: 20px;
}
.data-text {
    font-size: 20px;
}
</style>