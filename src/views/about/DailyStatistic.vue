<template>
    <div class="container">
        <div class="count" @click="where.displayDatePicker = true">
            <span v-if="where.date.length > 0">正在展示{{ filterDateDisplayText }}的数据，<br/></span>
            点击更换日期区间
        </div>
        <van-button
            :type="useEcharts ? 'warning' : 'danger'"
            @click="useEcharts = !useEcharts"
            :icon="useEcharts ? 'chart-trending-o' : 'cluster-o'"
        >
            {{ useEcharts ? '使用表格显示' : '使用图形显示' }}
        </van-button>
        <div class="count" v-if="data.length <= 0">无数据</div>
        <div class="lines" v-show="useEcharts">
            <div class="tip">订单数折线</div>
            <div id="pcLine" ref="pcLine"></div>
            <div class="tip">订单用户数折线</div>
            <div id="uvcLine" ref="uvcLine"></div>
            <div class="tip">订单金额折线</div>
            <div id="sLine" ref="sLine"></div>    
        </div>
        <div class="tables" v-show="!useEcharts">
            <div class="table" v-for="(v, k) in data" :key="k">
                <van-cell-group>
                    <van-cell title="日期" :value="v.d" />
                </van-cell-group>
                <van-cell-group>
                    <van-cell title="订单数" :value="v.pc" />
                </van-cell-group>
                <van-cell-group>
                    <van-cell title="订单用户数" :value="v.uvc" />
                </van-cell-group>
                <van-cell-group>
                    <van-cell title="订单总金额" :value="v.s / 1000" />
                </van-cell-group>
            </div>
        </div>
        
        <van-calendar
            v-model="where.displayDatePicker"
            type="range"
            @confirm="onDateChoosed"
            :min-date="where.minChooseableDate"
            allow-same-day
        >
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
import { dailyStatistic } from '@/api/business'
import config from '@/utils/config'
import echarts from 'echarts'
export default {
    name: 'OrderList',
    data() {
        return {
            bid: 0,
            where: {
                date: [
                    new Date(Date.parse(new Date()) - 86400000),
                    new Date(Date.parse(new Date()) + 86400000)
                ],
                displayDatePicker: false,
                minChooseableDate: new Date((new Date()).getFullYear() - 1 + '-01-01')
            },
            data: [],
            loading: false,
            pcLine: null,
            uvcLine: null,
            sLine: null,
            useEcharts: true
        }
    },
    created() {
        this.bid = localStorage.getItem(config.bidKey)
    },
    mounted() {
        this.initEcharts()
        this.getList()
    },
    methods: {
        drawEcharts() {
            this.pcLine.setOption({
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: this.data.map(v => {
                        return v.d
                    })
                },
                yAxis: {
                    type: 'value'
                },
                series: [{
                    data: this.data.map(v => {
                        return v.pc
                    }),
                    type: 'line',
                    areaStyle: {},
                    itemStyle : { normal: {label : {show: true, color: '#009688'}}}
                }]
            })
            this.uvcLine.setOption({
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: this.data.map(v => {
                        return v.d
                    })
                },
                yAxis: {
                    type: 'value'
                },
                series: [{
                    data: this.data.map(v => {
                        return v.uvc
                    }),
                    type: 'line',
                    areaStyle: {},
                    itemStyle : { normal: {label : {show: true, color: '#009688'}}}
                }]
            })
            this.sLine.setOption({
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: this.data.map(v => {
                        return v.d
                    })
                },
                yAxis: {
                    type: 'value'
                },
                series: [{
                    data: this.data.map(v => {
                        return (v.s - 0) / 100
                    }),
                    type: 'line',
                    areaStyle: {},
                    itemStyle : { normal: {label : {show: true, color: '#009688'}}}
                }]
            })
        },
        initEcharts() {
            this.pcLine = echarts.init(this.$refs.pcLine)
            this.uvcLine = echarts.init(this.$refs.uvcLine)
            this.sLine = echarts.init(this.$refs.sLine)
        },
        cleanEcharts() {
            this.pcLine.dispose()
            this.uvcLine.dispose()
            this.sLine.dispose()
        },
        date: (d, f) => date(d, f),
        cleanDateSelect () {
            this.where.displayDatePicker = false
            this.where.date = []
            this.getList()
        },
        onDateChoosed(e) {
            this.where.date = e
            this.where.displayDatePicker = false
            this.getList()
        },
        decode: text => decode(text),
        down() {
            this.data = []
            this.getList()
        },
        getList() {
            this.cleanEcharts()
            this.initEcharts()
            this.data = []
            const where = {}
            if (this.where.date.length > 0) {
                where['date_begin'] = date(this.where.date[0], 'Y-m-d')
                where['date_end'] = date(this.where.date[1], 'Y-m-d')
            }
            where.bid = this.bid
            const toast = this.$toast.loading({
                message: '加载中',
                forbidClick: true,
            })
            this.loading = true
            dailyStatistic(where).then(res => {
                if (res.code == 200) {
                    if (res.data.length <= 0) {
                        this.$toast('无数据')
                    } else {
                        this.data = res.data
                        this.drawEcharts()
                        if (this.data.length == 1) {
                            this.useEcharts = false
                            this.$toast('只有一条数据，推荐使用表格显示方式查看')
                        }
                    }
                } else {
                    this.$notify(res.msg)
                }
            }).catch(e => {
                console.error(e)
                this.$notify('获取数据时出现问题')
            }).finally(() => {
                setTimeout(() => {
                    toast.clear()
                }, 1000);
            })
        }
    },
    computed: {
        filterDateDisplayText() {
            if (this.where.date.length <= 0) {
                return '请点击选择日期'
            } else {
                return date(this.where.date[0], 'Y-m-d') + '~' + date(this.where.date[1], 'Y-m-d')
            }
        }
    }
}
</script>
<style scoped>
.container{
    width: 100vw;
    min-height: 100vh;
    /* background-color: #efeff5; */
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
.lines {
    margin-top: 20px;
}
#pcLine, #uvcLine, #sLine {
    width: 100%;
    height: 500px;
}
.tip {
    margin-left: 10px;
    width: 100%;
    border-left: solid 5px #34537c;
    padding-left: 5px;
}
.tables {
    margin-top: 20px;
}
.table {
    margin-bottom: 10px;
}
</style>