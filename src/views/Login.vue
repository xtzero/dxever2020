<template>
    <div class="login-container">
        <div style="font-size: 30px; font-weight: 900; width: 100%; text-align: center;margin-bottom: 5px;">
            大学印象商户数据看板
        </div>
        <div class="status">{{ status }}</div>
    </div>
</template>
<script>
import config from '@/utils/config'
import { ifBusinessExist } from '@/api/business'
export default {
    data() {
        return {
            status: ''
        }
    },
    created() {
        this.checkTokenInParams()
    },
    methods: {
        checkTokenInParams() {
            this.status = '登录中'
            const toast = this.$toast.loading({
                message: '加载商家id',
                forbidClick: true,
            });
            const bid = this.$route.query.bid
            if (!bid) {
                setTimeout(() => {
                    toast.clear()
                    this.status = '请从微信扫码进入'
                    this.$notify('请从微信扫码进入')
                    return
                }, 500)
            } else {
                ifBusinessExist({
                    bid: bid
                }).then(res => {
                    if (res.code == 200) {
                        if (res.data.ifBusinessExist == 1) {
                            toast.message = '取得登录凭证，正在登录'
                            localStorage.setItem(config.bidKey, bid)
                            this.$router.push('/')
                        } else {
                            this.status = '这个商户不存在，请重新扫码进入'
                            this.$notify('这个商户不存在，请重新扫码进入')
                        }
                    } else {
                        this.$notify(res.msg)
                    }
                }).catch(e => {
                    console.error(e)
                    this.$notify('登录时出问题拉，请等待修复')
                })
            }
        }
    }
}
</script>
<style>
    .login-container{
        min-height: 100vh;
        background-color: #efeff5;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .status {
        width: 100%;
        text-align: center;
    }
</style>
