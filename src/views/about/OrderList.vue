<template>
    <div class="container">
        <van-pull-refresh v-model="loading" @refresh="down">
            <div class="count">共 {{ count }} 条数据</div>
            <van-list
                v-model="loading"
                :finished="nomore"
                finished-text="没有更多了"
                @load="up"
            >
                <van-cell v-for="(item, k) in data" :key="k" value-class="listcell">
                    <van-image width="30" height="30" round="" :src="item.user.headimg"></van-image>    
                    <span class="usernickname">{{ decode(item.user.nickname) }}</span>
                    <div slot="extra">
                        {{ date(item.timestamp, 'Y-m-d') }} 
                        <van-tag type="success" size="large">￥{{ item.amount / 100 }}</van-tag>
                        <!-- <van-tag type="success" v-if="item.paid == 1">已付</van-tag>
                        <van-tag type="danger" v-else>未付</van-tag> -->
                    </div>
                </van-cell>
            </van-list>
        </van-pull-refresh>
         <!--顶部弹出  -->
        <div class="popup-handle" @click="where.displayPopup = true">
            <van-icon name="search" />
        </div>
        <van-popup v-model="where.displayPopup" position="right" :style="{ width: '60%', height: '100%' }" >
            <div class="filter-title">筛选条件</div>
            <van-field v-model="where.openid" label="openid" placeholder="请输入openid" clearable v-show="where.displayOpenidInput"/>
            <van-field v-model="where.nickname" label="昵称" placeholder="请输入用户昵称" clearable />
            <van-field v-model="filterDateDisplayText" label="日期" placeholder="请选择日期" :disabled="true" @click="where.displayDatePicker = true" type="textarea" autosize/>
            <div class="search-div">
                <van-button @click="onSearchBtnClick" type="primary" size="large">查询</van-button>
            </div>
        </van-popup>
        <van-calendar v-model="where.displayDatePicker" type="range" @confirm="onDateChoosed">
            <div slot="title">
                请选择日期
                <van-button type="danger" size="small" @click="cleanDateSelect">清除日期</van-button>
            </div>
        </van-calendar>
    </div>
</template>
<script>
import { date } from '@/utils/date'
import { decode, encode } from 'js-base64'
import { orderList } from '@/api/business'
import config from '@/utils/config'
export default {
    name: 'OrderList',
    data() {
        return {
            bid: 0,
            where: {
                nickname: '',
                openid: '',
                date: [],
                page: 0,
                limit: 20,
                displayPopup: false,
                displayDatePicker: false,
                displayOpenidInput: false
            },
            data: [],
            loading: false,
            nomore: false,
            count: 0
        }
    },
    created() {
        this.bid = localStorage.getItem(config.bidKey)
        const openid = this.$route.query.openid
        if (openid) {
            this.where.openid = openid
            this.where.displayOpenidInput = true
        }
    },
    methods: {
        date: (d, f) => date(d, f),
        cleanDateSelect () {
            this.where.displayDatePicker = false
            this.where.date = []
        },
        onSearchBtnClick() {
            this.where.displayPopup = false
            this.down()
        },
        onDateChoosed(e) {
            this.where.date = e
            this.where.displayDatePicker = false
        },
        decode: text => decode(text),
        down() {
            this.nomore = false
            this.data = []
            this.where.page = 1
            this.getList()
        },
        up() {
            this.where.page ++
            this.getList()
        },
        getList() {
            const where = {}
            if (this.where.nickname != '') {
                where.nickname = encode(this.where.nickname)
            }
            if (this.where.openid != '') {
                where.openid = this.where.openid
            }
            if (this.where.date.length > 0) {
                where['date_begin'] = date(this.where.date[0], 'Y-m-d')
                where['date_end'] = date(this.where.date[1], 'Y-m-d')
            }
            where.page = this.where.page
            where.limit = this.where.limit
            where.bid = this.bid
            this.loading = true
            orderList(where).then(res => {
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
                this.$notify('获取列表时出现问题')
                this.nomore = true
            }).finally(() => {
                this.loading = false
            })
        }
    },
    computed: {
        filterDateDisplayText() {
            if (this.where.date.length <= 0) {
                return '请点击选择日期'
            } else {
                return date(this.where.date[0], 'Y-m-d') + '-' + date(this.where.date[1], 'Y-m-d')
            }
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
    margin: 100%;
    font-size: 15px;
    color: gray;
    margin: 5px 10px;
}
.listcell {
    display: flex;
    flex-direction: row;
    align-items: center;
}
.usernickname {
    margin-left: 10px;
}
.popup-handle {
    position: fixed;
    width: 30px;
    height: 60px;
    background-color: #f00;
    right: -10px;
    bottom: 10%;
    border-radius: 10px 0 0 10px;
    color: white;
    font-size: 20px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding-left: 5px;
}
.filter-title {
    font-size: 20px;
    margin: 20px 20px;
}
.search-div {
    display: flex;
    flex-direction: row;
    justify-content: center;
    margin-top: 10px;
}
</style>