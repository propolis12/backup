import axios from "axios";
import $ from "jquery";


export function fetchOwnedImages() {
   return axios.get('/owned/images');

}


export function fetchImage(filename) {
    return axios.get('/photos/'.filename);
}

export function fetchLatestImages() {
    return axios.get('/latest/uploaded/photo');
}

export function deleteImage(name) {
    return axios.delete('/delete/image/'+ name);
}

export function fetchImages() {
    return axios.get('/get/images');
}