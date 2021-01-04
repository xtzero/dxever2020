<template>
  <div class="home-container">
    <van-swipe class="my-swipe" :autoplay="3000" indicator-color="white">
      <van-swipe-item v-for="(pic, picIndex) in baseInfo.pic" :key="picIndex">
        <van-image
          :src="pic"
          fit="cover"
          height="300"
          @click="preview(picIndex)"
        >
        </van-image>
      </van-swipe-item>
    </van-swipe>
    <div class="name">
      <div class="name-text">{{ baseInfo.name }}</div>
      <div class="bid">bid: {{ baseInfo.bid }}</div>
    </div>
    <div class="subtitle">
      <van-image src="/positionIcon.png" width="20" height="20" fit="cover"></van-image>
      <div class="location">{{ baseInfo.location }}</div>
      <div class="region">{{ baseInfo.region }}</div>
    </div>
    <!-- <div class="amounts">
      <div class="amount">
        <div class="amount-num">{{ baseInfo.onediscount ? baseInfo.onediscount / 10 + '%' : '-' }}</div>
        <div class="amount-title">首次折扣</div>
      </div>
      <div class="amount">
        <div class="amount-num">{{ baseInfo.flimit ? baseInfo.flimit / 1000 + ":1" : '-' }}</div>
        <div class="amount-title">首单消费限额</div>
      </div>
      <div class="amount">
        <div class="amount-num">{{ baseInfo.twodiscount ? baseInfo.twodiscount / 10 + '%' : '-' }}</div>
        <div class="amount-title">前10次新客折扣</div>
      </div>
      <div class="amount">
        <div class="amount-num">{{ baseInfo.threediscount ? baseInfo.threediscount / 10 + '%' : '-' }}</div>
        <div class="amount-title">老客折扣</div>
      </div>
    </div> -->
    <div class="amounts" v-if="!amount.today">
      <div class="usercard-title">今日暂无数据</div>
    </div>
    <div class="amounts" v-if="amount.today">
      <div class="usercard-title">今日数据</div>
      <div class="amount">
        <div class="amount-num">
          {{ amount.today.d }}
        </div>
        <div class="amount-title">日期</div>
      </div>
      <div class="amount">
        <div class="amount-num">
          {{ amount.today.pc }}
          <van-tag :type="pcTag < 0 ? 'danger' : 'success'" v-if="pcTag != 0" size="large" >
            {{ pcTag }}
          </van-tag>
        </div>
        <div class="amount-title">订单数</div>
      </div>
      <div class="amount">
        <div class="amount-num">
          {{ amount.today.uvc }}
          <van-tag :type="uvcTag < 0 ? 'danger' : 'success'" v-if="uvcTag != 0" size="large">
            {{ uvcTag }}
          </van-tag>
        </div>
        <div class="amount-title">订单人次</div>
      </div>
      <div class="amount">
        <div class="amount-num">
          {{ (amount.today.s - 0) / 1000 }}
          <van-tag :type="sTag < 0 ? 'danger' : 'success'" v-if="sTag != 0" size="large">
            {{ sTag }}
          </van-tag>
        </div>
        <div class="amount-title">营业额</div>
      </div>
    </div>
    <div class="usercard">
      <div class="usercard-title">店主</div>
      <div class="usercard-info">
        <van-image :src="baseInfo.owner.headimg ? baseInfo.owner.headimg : ''" round width="40" height="40"></van-image>
        <div class="usercard-name">
            <div class="usercard-name-text">
              {{ decode(baseInfo.owner.nickname) }}
              <div class="usercard-vip">{{ encodeVip(baseInfo.owner.vip) }}</div>
            </div>
            <div class="usercard-name-openid">{{ encodeOpenid(baseInfo.owner.openid) }}</div>
        </div>
      </div>
    </div>
    <!-- <div class="usercard">
      <div class="usercard-title">地推人员</div>
      <div class="usercard-info">
        <van-image :src="baseInfo.jober.headimg ? baseInfo.jober.headimg : ''" round width="40" height="40"></van-image>
        <div class="usercard-name">
            <div class="usercard-name-text">
              {{ decode(baseInfo.jober.nickname) }}
              <div class="usercard-vip">{{ encodeVip(baseInfo.jober.vip) }}</div>
            </div>
            <div class="usercard-name-openid">{{ encodeOpenid(baseInfo.jober.openid) }}</div>
        </div>
      </div>
    </div> -->
    <div class="foot">已登录 bid: {{ baseInfo.bid }}</div>
  </div>
</template>

<script>
import config from '@/utils/config'
import { baseInfo, dailyStatistic } from '@/api/business'
import { decode } from 'js-base64';
import { date } from '@/utils/date'
export default {
  name: 'Home',
  data() {
    return {
      bid: null,
      baseInfo: {},
      amount: {
        today: null,
        yesterday: null
      }
    }
  },
  created() {
    this.bid = localStorage.getItem(config.bidKey)
  },
  mounted() {
    this.getBusinessBaseInfo()
    this.getDailyStatistic()
  },
  methods: {
    getDailyStatistic() {
      const yesterdayDate = new Date(Date.parse(new Date()) - 86400000)
      const todayDate = new Date()
      const tomorrowDate = new Date(Date.parse(new Date()) + 86400000)

      dailyStatistic({
        bid: this.bid,
        'date_begin': date(yesterdayDate, 'Y-m-d'),
        'date_end': date(tomorrowDate, 'Y-m-d')
      }).then(res => {
        if (res.code == 200) {
          res.data.forEach(v => {
            if (v.d == date(yesterdayDate, 'Y-m-d')) {
              this.amount.yesterday = v
            }
            if (v.d == date(todayDate, 'Y-m-d')) {
              this.amount.today = v
            }
          })
        } else {
          this.$notify(res.msg)
        }
      }).catch(e => {
        console.error(e)
        this.$notify('获取统计数据时出现问题，请刷新重试')
      })
    },
    encodeVip(vip) {
      switch(vip) {
        case 1: return '永久vip'
        case 2: return '试用vip'
        case 3: return 'vip月卡'
      }
    },
    decode: (text) => decode(text),
    encodeOpenid: (openid) => openid.substr(0, 10) + '...',
    getBusinessBaseInfo() {
      const toast = this.$toast.loading({
        message: '加载商家基本信息',
        forbidClick: true,
      })
      baseInfo({
        bid: this.bid
      }).then(res => {
        if (res.code == 200) {
          this.baseInfo = res.data
        } else {
          this.$notify(res.msg)
        }
      }).catch(e => {
        console.error(e)
        this.$notify('获取基本信息时出现问题')
      }).finally(() => {
        toast.clear()
      })
    },
    preview(i) {
      this.$imagePreview(this.baseInfo.pic, i)
    }
  },
  computed: {
    pcTag() {
      if (this.amount.yesterday && this.amount.today) {
        const value = this.amount.today.pc - this.amount.yesterday.pc
        if (value > 0) {
          return '+' + value
        } else {
          return value
        }
      } else {
        return false
      }
    },
    uvcTag() {
      if (this.amount.yesterday && this.amount.today) {
        const value = this.amount.today.uvc - this.amount.yesterday.uvc
        if (value > 0) {
          return '+' + value
        } else {
          return value
        }
      } else {
        return false
      }
    },
    sTag() {
      if (this.amount.yesterday && this.amount.today) {
        const value = (((this.amount.today.s - 0) / 100) - ((this.amount.yesterday.s - 0) / 100)).toFixed(2)
        if (value > 0) {
          return '+' + value
        } else {
          return value
        }
      } else {
        return false
      }
    }
  }
}
</script>
<style>
  .home-container{
      min-height: 100vh;
      background-color: #efeff5;
      display: flex;
      flex-direction: column;
      padding-bottom: 66px;
  }
  .name {
    margin-top: 20px;
    display: flex;
    flex-direction: row;
    align-items: flex-end;
  }
  .name-text {
    font-size: 30px;
    font-weight: 500;
  }
  .bid {
    margin-left: 10px;
    background-color: #232225;
    color: #EEC643;
    padding: 2px 5px;
    border-radius: 3px;
    margin-bottom: 5px;
  }
  .subtitle {
    display: flex;
    flex-direction: row;
    align-items: center;
  }
  .location {
    margin-left: 5px;
  }
  .region {
    margin-left: 10px;
  }
  .name, .subtitle {
    margin-left: 10px;
  }
  .amounts {
    margin-top: 20px;
    background-color: #fff;
    display: flex;
    flex-direction: column;
    box-shadow: 0 8px 12px #ebedf0;
  }
  .amount {
    display: flex;
    flex-direction: row;
    /* justify-content: space-between; */
    align-items: center;
    height: 50px;
  }
  .amount-num {
    margin-left: 10px;
    font-size: 25px;
    color: #EEC643;
    width: 50%;
  }
  .amount-title {
    margin-left: 10%;
  }
  .usercard {
    margin-top: 20px;
    background-color: #fff;
    display: flex;
    flex-direction: column;
    box-shadow: 0 8px 12px #ebedf0;
  }
  .usercard-title {
    margin-left: 15px;
    color: gray;
    font-size: 15px;
    margin-top: 5px;
  }
  .usercard-info {
    display: flex;
    flex-direction: row;
    margin-top: 20px;
    margin-left: 10px;
  }
  .usercard-name {
    margin-left: 10px;
  }
  .usercard-name-text {
    font-size: 20px;
    display: flex;
    flex-direction: row;
    align-items: flex-end;
    margin-bottom: 10px;
  }
  .usercard-vip {
    margin-left: 10px;
    margin-bottom: 3px;
    font-size: 13px;
    background-color: #232225;
    color: #EEC643;
    padding: 2px 5px;
    border-radius: 3px;
  }
  .usercard-name-openid {
    font-size: 15px;
    color: gray;
    margin-bottom: 10px;
  }
  .foot {
    width: 100%;
    text-align: center;
    font-size: 13px;
    color: gray;
    margin-top: 10px;
  }
</style>
