<template>
  <div class="common-layout">
    <div class="content">
      <div class="top">
      <div class="header">
        <img alt="logo" class="logo" src="https://www.dxever.com/fei/WEB/images/logo.png" />
        <span class="title">大学印象返利卡</span>
      </div>
      <div class="desc">后台管理系统</div>
    </div>
    <div class="login">
      <a-form>
        <a-form-item>
          <a-input
            size="large"
            v-model="username"
          >
            <a-icon slot="prefix" type="user" />
          </a-input>
        </a-form-item>
        <a-form-item>
          <a-input
            size="large"
            type="password"
            v-model="password"
          >
            <a-icon slot="prefix" type="lock" />
          </a-input>
        </a-form-item>
        <a-form-item>
          <a-button
            :loading="loading"
            style="width: 100%;margin-top: 24px"
            size="large"
            type="primary"
            @click="login"
          >
            登录
          </a-button>
        </a-form-item>
      </a-form>
    </div>
    </div>
  </div>
</template>
<script>
export default {
    name: 'login',
    data: () => ({
      username: '',
      password: '',
      loading: false
    }),
    mounted() {
      console.log(this.$store.getters.user.access_token)
    },
    methods: {
      login() {
        if (!this.username) {
          this.$message.error('请输入用户名')
          return
        }
        if (!this.username) {
          this.$message.error('请输入密码')
          return
        }
        this.$store.dispatch('user/login', {
          username: this.username, 
          password: this.password
        }).then(() => {
          if (this.$route.query.redirect_path) {
              this.$router.push(this.$route.query.redirect_path)
          } else {
              this.$router.push('/')
          }
        })
      }
    }
}
</script>
<style lang="less" scoped>
  .common-layout{
    .top {
      text-align: center;
      .header {
        height: 44px;
        line-height: 44px;
        a {
          text-decoration: none;
        }
        .logo {
          height: 44px;
          vertical-align: top;
          margin-right: 16px;
        }
        .title {
          font-size: 33px;
          color: black;
          font-family: 'Myriad Pro', 'Helvetica Neue', Arial, Helvetica, sans-serif;
          font-weight: 600;
          position: relative;
          top: 2px;
        }
      }
      .desc {
        font-size: 14px;
        color: black;
        margin-top: 12px;
        margin-bottom: 40px;
      }
    }
    .login{
      width: 368px;
      margin: 0 auto;
      @media screen and (max-width: 576px) {
        width: 95%;
      }
      @media screen and (max-width: 320px) {
        .captcha-button{
          font-size: 14px;
        }
      }
      .icon {
        font-size: 24px;
        color: black;
        margin-left: 16px;
        vertical-align: middle;
        cursor: pointer;
        transition: color 0.3s;

        &:hover {
          color: black;
        }
      }
    }
  }
  .common-layout{
  display: flex;
  flex-direction: column;
  height: 100vh;
  overflow: auto;
  background-color: #efeff5;
  background-image: url('https://gw.alipayobjects.com/zos/rmsportal/TVYTbAXWheQpRcWDaDMu.svg');
  background-repeat: no-repeat;
  background-position-x: center;
  background-position-y: 110px;
  background-size: 100%;
  .content{
    padding: 32px 0;
    flex: 1;
    @media (min-width: 768px){

      padding: 112px 0 24px;
    }
  }
}
</style>
