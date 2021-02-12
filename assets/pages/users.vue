<template>
  <div class="container-fluid ">
      <!--<Navbar :title="message"/>-->
    <div class="row">
      <div class="TitleUsers d-flex justify-content-center rounded">
            <h2>Registered Users</h2>
      </div>
    <div
        class="row ml-3">
      <h4>please type your password again </h4>
      <label>
        <input v-model="password"  class="form-control" type="password"/>
      </label>
      <input type="button" @click="authenticate" value="send">
    </div>
      <div>
        <input type="button" @click="clearCookie" value="clear">
      </div>
    </div>
    <div class="tabulka">



      <UserList class=""
          :key="users"
          @delete-user="refresh"
          :users="users"
          :loading="loading"
      />
    </div>
      <Footer :footer-message="footerMessage"></Footer>
  </div>





</template>

<script>
import Navbar from '@/components/navbar';
import Footer from '@/components/footer';
import axios from 'axios';
import UserList from '@/components/user_list/index';
import {fetchUsers, fetchUsersToken, refreshToken} from '@/services/users-service';
import { authenticateUser} from "@/services/users-service";
import * as array from "core-js";



export default {
  name: 'Users',
  components: {
      Navbar,
      Footer,
    UserList,
  },



  data() {
    return {
        footerMessage: 'My own Photocloud made with care 2021 ',
        users: [],
      loading: false,
      logged: false,
      componentKey: 0,
      password: '',
      token: '',
      showIfLogged: false,
    };
  },

  methods: {
    refresh(id) {
      console.log(id + ' poslane do users.vue')
      /* deletovanie z pola !!!!!!! */
      this.users = this.users.filter(el => el.id !== id);
    },

    clearCookie() {
      document.getElementById('logout').click();
    },

    async authenticate() {
      console.log('som tu');
      //let response = '';
      const headers = {
        'Content-Type': 'application/json'
      }
      let response = await authenticateUser(window.username, this.password)/*axios.post('/api/login_check', {username : window.username , password : this.password }, {
        headers : headers
      })*/
      console.log(response);
      this.token = response.data.token;
      console.log(this.token);
      //window.token = this.token
      response = await fetchUsersToken(/*this.token*/)
      this.users = response.data['hydra:member'];
      console.log(this.users);
      //let response = await authenticateUser(this.password, window.username)
      //let response = await axios.get('https://127.0.0.1:8000/api/users');
      /*let response = axios({
        method: 'post',
        headers: "'Content-Type': 'application/json'",
        url: '/api/login_check',
        data: {
          username: window.username,
          password: this.password
        }
      });*/
      //console.log(response);
      //console.log(response);
    }



  },

  async created() {
      /*axios.get('/api/users').then((response) => {
        console.log(response);
      })*/
    //response = await this.authenticate();
    //await refreshToken();
    if (window.logged) {
      console.log('tuuuuu');
      response = await fetchUsersToken(/*this.token*/);
      this.users = response.data['hydra:member'];
    }

    this.loading = true
    let response;

    try {
      //response = await fetchUsers();
      //console.log(response);
      this.loading = false;

    } catch (e) {
      this.loading = false;

      return;
    }
    //this.users = response.data['hydra:member'];


  },
 async mounted() {
    console.log("asdadasdadaasfaf");
   //let response = await authenticateUser(window.username, this.password)
  if (await refreshToken().then(({status}) => {
    if (status === 200 || status === 204) {
      this.logged = true;
      console.log("sadafsfsdf");
    }
  }));

  console.log(this.logged);
  if (this.logged) {
    let response = await fetchUsersToken();
    this.users = response.data['hydra:member'];
    console.log(this.users);
  }
   //this.users = response.data['hydra:member'];
     /*if ( response.status === 201 ) {
      this.showIfLogged = false;
   }
    this.users = response.data['hydra:member'];*/
  }
}



</script>