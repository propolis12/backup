import './bootstrap';
import './styles/mainPage.css';


import {createApp} from "vue";
import MainPage from "@/pages/mainPage.vue";
require('bootstrap')
import axios from "axios";

createApp(MainPage).mount('#main')


/** to show the image filename in form field */
$('.custom-file-input').on('change', function (event) {
    var inputFile = event.currentTarget;
    $(inputFile).parent()
        .find('.custom-file-label')
        .html(inputFile.files[0].name);

})


