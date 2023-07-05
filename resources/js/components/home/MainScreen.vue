<template>
  <div>
        <Header></Header>
          <div>Content</div>
         <Footer></Footer>
  </div>
</template>

<script>
 
import Footer from '../narbar/Footer.vue'
import Header from '../narbar/Header.vue'
export default {
  components: { Header, Footer },
  created() {
      console.log("window.Laravel");
      console.log(window.Laravel);
    //  this.token = window.Laravel.access_token ;
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