import {fetchLatestImages, fetchOwnedImages} from "@/services/images-service";

Dropzone .autoDiscover = false;
import './bootstrap';
import './styles/mainPage.css';
import dropzone from "dropzone";
import 'dropzone/dist/dropzone.css';
import $ from 'jquery';
import MainPage from "./pages/mainPage.vue";
import { createApp } from "vue";

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


$(document).ready(function() {
    $('#dropzone').hide();

    initializeDropzone();
    let response;
    renderImages().then(r => response);
    //getFiles();
   // var imagesList = new ImageList($('.js-photo-list'));
});

function initializeDropzone() {
    var newNames = [];
    var formElement = document.querySelector('.js-reference-dropzone');
    if (!formElement) {
        return;
    }
    var dropzone = new Dropzone(formElement, {
        paramName: 'dropzone',
        acceptedFiles: ".jpeg,.jpg,.png,.gif",
        renameFile: function (file) {
            let newName = new Date().getTime() + '_' + file.name;
             newNames.push(newName);
             return newName;
        },
        init: function() {
            this.on('success', function(file, data) {
                console.log(newNames);


            });

            this.on('error', function(file, data) {
                if (data.detail) {
                    this.emit('error', file, data.detail);
                }
            });

            this.on('queuecomplete', function (file) {
                   //this.appendImage(file);
                appendImage(newNames).then(r => { newNames = [];
                    //alert("All files have uploaded ");
                    setTimeout(() => $('#dropzone').html(""),2000)
                    //console.log(r);
                });
            })
        }
    });
}


async function appendImage(names) {
    for(let i = 0; i < names.length; i++) {
    //var image;
    //image = await fetchLatestImages();
    console.log(names[i]);
    $('#photo-list').append($('<img>',{src:'/latest/photos/'+ names[i], alt: 'photo'}))
    }
}



async function renderImages() {
    var ownedImages = await fetchOwnedImages();
    //console.log(ownedImages.data[0]['filename']);
    //console.log(ownedImages.data.length);

    for(var i = 0 ; i < ownedImages.data.length ; i++) {
        $('#photo-list').append($('<img>',{src:'/photo/'+ ownedImages.data[i]['filename'], alt: 'photo '+i}))
    }
}




    $('#uploadIcon').click(function () {
            $('#dropzone').toggle();
    })



