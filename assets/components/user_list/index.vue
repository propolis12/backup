<template>

    <div class="col-12">
      <div class="mt-4">
        <loading v-show="loading" />

        <h5 v-show="!loading && users.length === 0"
        >
          Submit your password to view registered users
        </h5>
      </div>
    </div>
    <div>
      <div class="col-4 offset-4 justify-content-center">
        <table class="table">
            <thead class="thead-dark">
            <tr>
              <th scope="col">id</th>
              <th scope="col">Username</th>
              <th scope="col">email</th>
              <th scope="col">Firstname</th>
              <th scope="col">Lastname</th>
              <th scope="col"></th>
            </tr>
            </thead>
          <tbody>
            <user-card
                @delete-user="deleteUser"
                v-for="user in users"
                v-show="!loading"
                :key="user['@id']"
                :user="user"
            />
          </tbody>
        </table>
      </div>
    </div>

</template>


<script>
import UserCard from '@/components/user_list/user-card';
import Loading from '@/components/loading'

export default {
  name: 'UserList',
  components: {
    UserCard,
    Loading,
  },
  props: {
    users: {
      type: Array,
      required: true,
    },
    loading: {
      type: Boolean,
      required: true,
    },
  },
  methods:  {
    deleteUser(id) {
      console.log( id + '  poslane do index.vue');
      this.$emit('delete-user', id);

    },
  },

}


</script>