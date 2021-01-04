<template>
    <div class="container">
        <div class="item-div"></div>
        <div class="item" @click="jump('/userList')">
            <van-image width="30" height="30" class="item-icon" src="/userIcon.png"></van-image>
            <div class="item-name">用户数据</div>
        </div>
        <div class="item" @click="jump('/orderList')">
            <van-image width="30" height="30" class="item-icon" src="/orderIcon.png"></van-image>
            <div class="item-name">订单数据</div>
        </div>
        <div class="item-div"></div>
        <div class="item" @click="jump('/dailyStatistic')">
            <van-image width="30" height="30" class="item-icon" src="/dailyIcon.png"></van-image>
            <div class="item-name">每日统计</div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'About',
    data() {
        return {

        }
    },
    methods: {
        jump(url) {
            this.$router.push(url)
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
.item {
    width: 100%;
    height: 50px;
    display: flex;
    flex-direction: row;
    /* justify-content: space-between; */
    align-items: center;
    background-color: #fff;
    margin-bottom: 1px;
}
.item:active {
    background-color: #F2F6FC;
}
.item-icon {
    margin-left: 10px;
}
.item-name {
    margin-left: 5px;
}
.item-div {
    height: 20px;
}
</style>