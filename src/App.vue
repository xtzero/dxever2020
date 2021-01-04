<template>
    <div id="app">
        <router-view></router-view>
    </div>
</template>

<script>
export default {
  name: 'App',
  components: {
    
  },
  mounted() {
    const userinfoFromLocalStorage = localStorage.getItem('loginRes')
    if (userinfoFromLocalStorage) {
      this.$store.commit('user/loginSuccess', JSON.parse(userinfoFromLocalStorage))
    } else {
      this.$router.push('/login')
    }
  }
}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  width: 100%;
  height: 100%;
}
</style>
