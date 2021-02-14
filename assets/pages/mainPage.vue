<template>

  <img alt="fotka" src="/photos/istockphoto-951945718-612x612-6027bc1023495.jpg">




</template>


<script>
import {fetchImage} from "@/services/images-service";

export default {
  name: 'MainPage',
  data () {


  },

  methods: {

    log() {
      console.log("adfadfadfafa")
    },

    getImage() {
        fetchImage('istockphoto-951945718-612x612-6027bc1023495.jpg')

    }



  }






}







</script>



<style>










</style>