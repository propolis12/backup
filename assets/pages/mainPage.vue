<template>
  <div class="container-fluid">
  <div class="row">
    <div class="col-2">
    </div>
    <img alt="fotka" src="/photos/istockphoto-951945718-612x612-6027bc1023495.jpg">
    <p>edwefewferfgerwgwwrgwrg</p>
  </div>
  <div>
    <div class="row"
         :key="imageNames">
      <div class="col-2 px-0"
        v-for="image in this.imageNames">
        <img :src="'/photo/'+image" alt="image">


      </div>

    </div>
    <vue-dropzone ref="myVueDropzone" id="dropzone" :options="dropzoneOptions"></vue-dropzone>
  </div>


  </div>
</template>


<script>
import {fetchImage, fetchOwnedImages} from "@/services/images-service";
import vue2Dropzone from 'vue2-dropzone'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'
export default {
  name: 'MainPage',
  components: {
    vueDropzone: vue2Dropzone
  },
  data () {
    return {
    ownedImages: [],
      imageNames: [],
      dropzoneOptions: {
        url: 'https://httpbin.org/post',
        thumbnailWidth: 150,
        maxFilesize: 0.5,
        headers: { "My-Awesome-Header": "header value" }
      }

    }
  },

  methods: {

    log() {
      console.log("adfadfadfafa")
    },

    getImage() {
        fetchImage('istockphoto-951945718-612x612-6027bc1023495.jpg')

    }



  },

  async mounted() {

    //console.log(this.ownedImages);
    //console.log(this.ownedImages.data);
    //console.log(this.ownedImages.data[0]['filename']);


  },
  async created () {
    this.ownedImages = await fetchOwnedImages();
    for(var i = 0 ; i < this.ownedImages.data.length; i++) {
      this.imageNames[i] = this.ownedImages.data[i]['filename'];
    }

  }



}







</script>



<style>










</style>