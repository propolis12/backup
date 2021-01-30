<template>
  <!--<div class=" users-column col-4 d-flex justify-content-center ">-->
    <tr>
      <th scope="row"> {{ user.id }} </th>
      <td>{{ user.username }}</td>
      <td>{{  user.email }}</td>
      <td>{{  user.firstname }}</td>
      <td>{{  user.lastname }}</td>
      <td><i class="fas fa-trash-alt" @click="dltUser()"></i></td>
    </tr>

   <!-- <span>{{ "id:" +  user.id +    "  username: "  + user.username + " email:  " + user.email + " ".repeat(5)}}<i class="fas fa-trash-alt" @click="dltUser()"></i></span>-->

 <!-- </div> -->


</template>



<script>
import {deleteUser} from "@/services/users-service";
import axios from "axios";

export default {
  name: 'UserCard',
  props: {
    user: {
      type: Object,
      required: true,
    },
  },
    methods: {
      async  dltUser() {
       console.log(this.user.id)
       /*axios.delete('/api/users/' + this.user.id).then((response) => {
         console.log(response)})*/

        await deleteUser(this.user.id);
       //this.users = response.data['hydra:member'];
        console.log("klikol som tlacitko");
        this.$emit('delete-user', this.user.id);
      },

    },





}


</script>