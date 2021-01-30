import axios from "axios";


/**
 *
 * @returns {Promise}
 */
export function fetchUsers() {
   return  axios.get('/api/users');

}


export function deleteUser(id) {
   console.log(id);
   return  axios.delete('/api/users/'+ id )
}