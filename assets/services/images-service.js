import axios from "axios";


export function fetchOwnedImages() {
   return axios.get('/owned/images');

}


export function fetchImage(filename) {
    return axios.get('/photos/'.filename);
}


