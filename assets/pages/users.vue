<template>
  <div class="container-fluid">
      <Navbar :title="message"/>
    <div class="row">
      <div class="TitleUsers d-flex justify-content-center rounded">
            <h2>Registered Users</h2>
      </div>
    </div>
    <div class="spaceDiv d-flex pt-2">

    </div>

      <div class="row">
          <div
              v-for="user in users"
              :key="user['@id']"
              class=" users-column col-4 d-flex justify-content-center mb-2 pb-2 "
          >
            <span>{{ "id:" +  user.id +    "  username: "  + user.username + " ".repeat(5) + " email:  " + user.email + " ".repeat(5)}}<i class="fas fa-trash-alt"></i></span>
          </div>
      </div>
      <Footer :footer-message="footerMessage"></Footer>
  </div>





</template>

<script>
import Navbar from '@/components/navbar';
import Footer from '@/components/footer';
import axios from 'axios';
export default {
  name: 'Users',
  components: {
      Navbar,
      Footer,
  },
  data() {
    return {
       firstname: 'sinassasergrsgsdgfdbgffedfseffffdfsdefdftrgrgga',
        message: 'to ked raz je cvina to je furt len spina',
        footerMessage: 'My own Photocloud made with care 2021 ',
        users: [],
    };
  },
  /*async */mounted() {
      axios.get('/api/users').then((response) => {
        console.log(response);
        this.users = response.data['hydra:member'];
        //const response = await axios.get('/api/users');
      })
  },
};



</script>