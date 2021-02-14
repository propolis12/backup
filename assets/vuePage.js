//import Vue from 'vue';
import './styles/vuePage.css';
import './bootstrap';
//import Dropzone from 'dropzone';
//Dropzone.autoDiscover = false;
require('bootstrap')
import { createApp, compile } from 'vue';
import Users from './pages/users';
import axios from "axios";
import {refreshToken} from "@/services/users-service";

let isRefreshing = false;
let subscribers = [];

axios.interceptors.response.use(response => {
 return response;
}, err => {
 const {
  config,
     response: { status, data }
 } = err;

 const originalRequest = config;

 if (originalRequest.url.includes('/api/login_check')) {
    return Promise.reject(err);
 }

 if (status === 401 && data.message === "JWT Token not found") {
   if(!isRefreshing) {
     isRefreshing = true;
     refreshToken().then(({status}) => {
         if (status === 200 || status === 204) {
             isRefreshing = false;
         }
         subscribers = [];
       })
         .catch(error =>  {
             console.error(error)
         });

   }

   const requestSubscribers = new Promise(resolve => {
       subscribeTokenRefresh(() => {
           resolve(axios(originalRequest));
       });
   });
   onRefreshed();
   return requestSubscribers;
 }
})

subscribers = [];

function subscribeTokenRefresh(cb) {
    subscribers.push(cb);
}

function onRefreshed() {
    subscribers.map(cb => cb());
}


 createApp(Users).mount('#users')



/** to show the image filename in form field */
$('.custom-file-input').on('change', function (event) {
    var inputFile = event.currentTarget;
    $(inputFile).parent()
        .find('.custom-file-label')
        .html(inputFile.files[0].name);

})