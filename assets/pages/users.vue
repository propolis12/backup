<template>
  <div class="container-fluid ">
      <!--<Navbar :title="message"/>-->
    <div class="row">
      <div class="TitleUsers d-flex justify-content-center rounded">
            <h2>Registered Users</h2>
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
import { fetchUsers } from '@/services/users-service';
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
       firstname: 'sinassasergrsgsdgfdbgffedfseffffdfsdefdftrgrgga',
        message: 'to ked raz je cvina to je furt len spina',
        footerMessage: 'My own Photocloud made with care 2021 ',
        users: [],
      loading: false,
      componentKey: 0,
    };
  },

  methods: {
    refresh(id) {
      console.log(id + ' poslane do users.vue')
      /* deletovanie z pola !!!!!!! */
      this.users = this.users.filter(el => el.id !== id);
    }
  },

  async created() {
      /*axios.get('/api/users').then((response) => {
        console.log(response);
      })*/
    this.loading = true
    let response;

    try {
      response = await fetchUsers();
      this.loading = false;

    } catch (e) {
      this.loading = false;

      return;
    }
    this.users = response.data['hydra:member'];


  },

};



</script>