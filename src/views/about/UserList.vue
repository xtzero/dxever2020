<template>
    <div class="container">
        <van-pull-refresh v-model="loading" @refresh="pullDown">
            <div class="count">共 {{ count }} 名用户，点击用户可以查看该用户的订单。</div>
            <van-list
                v-model="loading"
                :finished="nomore"
                finished-text="没有更多了"
                @load="pullUp"
            >
                <van-cell v-for="(item, k) in data" :key="k" value-class="listcell" @click="jumpToOrder(item.openid)">
                    <van-image width="30" height="30" round="" :src="item.user.headimg"></van-image>    
                    <span class="usernickname">{{ decode(item.user.nickname) }}</span>
                    <div class="list-cell-extra" slot="extra">{{ item.num}} 次消费 <van-icon name="arrow" /></div>
                </van-cell>
            </van-list>
        </van-pull-refresh>   
    </div>
</template>
<script>
import { decode } from 'js-base64'
import config from '@/utils/config'
import { userList } from '@/api/business'
export default {
    name: 'UserList',
    data() {
        return {
            bid: 0,
            loading: false,
            nomore: false,
            data: [],
            page: 0,
            count: 0
        }
    },
    created() {
        this.bid = localStorage.getItem(config.bidKey)
    },
    methods: {
        jumpToOrder(openid) {
            this.$router.push({
                path: '/orderList',
                query: {
                    openid: openid
                }
            })
        },
        decode: text => decode(text),
        getList() {
            this.loading = true
            userList({
                bid: this.bid,
                page: this.page,
                limit: 20
            }).then(res => {
                if (res.code == 200) {
                    this.count = res.data.count
                    if (res.data.data.length <= 0) {
                        this.nomore = true
                    } else {
                        res.data.data.forEach(v => {
                            this.data.push(v)
                        })
                    }
                } else {
                    this.$notify(res.msg)
                    this.nomore = true
                }
            }).catch(e => {
                console.error(e)
                this.$notify('加载列表时出现错误')
                this.nomore = true
            }).finally(() => {
                this.loading = false
            })
        },
        pullUp() {
            this.page ++
            this.getList()
        },
        pullDown() {
            this.page = 1
            this.nomore = false
            this.data = []
            this.getList()
        }
    }
}
</script>
<style scoped>
.container{
    min-height: 100vh;
    background-color: #efeff5;
    display: flex;
    flex-direction: column;
    padding-bottom: 66px;
}
.count {
    width: 100%;
    font-size: 15px;
    color: gray;
    margin: 10px;
}
.listcell {
    display: flex;
    flex-direction: row;
    align-items: center;
}
.usernickname {
    margin-left: 10px;
}
.list-cell-extra {
    display: flex;
    flex-direction: row;
    align-items: center;
}
</style>