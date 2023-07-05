<template>
  <div>
        <Header></Header>
          <div>{{token}}</div>
         <Footer></Footer>
  </div>
</template>

<script>
 
import Footer from '../narbar/Footer.vue'
import Header from '../narbar/Header.vue'
export default {
  components: { Header, Footer },
  created() {
      
      this.token = "Bearer "+ window.Laravel.access_token ;
      
  },
 
  data() {
    return {
      token:"",
      
    }
  },
}
</script>

<style>

</style>