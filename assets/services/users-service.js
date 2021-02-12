import axios from "axios";


/**
 *
 * @returns {Promise}
 */
export function fetchUsers() {
   return  axios.get('/api/users');

}

export function  fetchUsersToken(/*token*/) {
 //return axios.get('/api/users',  { headers: { 'Authorization' : 'Bearer ' + token } } );
 return axios.get('/api/users',  {withCredentials : true} );
}

export function deleteUser(id) {
   console.log(id);
   return  axios.delete('/api/users/'+ id )
}

/**
 *
 * @returns {Promise}
 */
export function authenticateUser(username, password) {
   const headers = {
      'Content-Type': 'application/json'
   }
   return axios.post('/api/login_check', {username : username , password : password }, {
      headers : headers,
      withCredentials: true
   })


}

export function refreshToken() {
   return new Promise((resolve, reject) => {
      axios
          .post(`/`)
          .then(response => {
             resolve(response);
          })
          .catch(error => {
             reject(error);
          });
      });
}
