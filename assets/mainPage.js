import {fetchLatestImage, fetchOwnedImages} from "@/services/images-service";

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


    initializeDropzone();
    let response;
    renderImages().then(r => response);
    //getFiles();
   // var imagesList = new ImageList($('.js-photo-list'));
});

function initializeDropzone() {
    var formElement = document.querySelector('.js-reference-dropzone');
    if (!formElement) {
        return;
    }
    var dropzone = new Dropzone(formElement, {
        paramName: 'dropzone',
        init: function() {
            this.on('success', function(file, data) {
                appendImage().then(r => {
                    console.log(r);
                });

            });

            this.on('error', function(file, data) {
                if (data.detail) {
                    this.emit('error', file, data.detail);
                }
            });

            this.on('uploadFile', function (file) {
                   this.appendImage(file);
            })
        }
    });
}


async function appendImage() {
    var image;
    image = await fetchLatestImage();
    console.log(image.data);
    $('#photo-list').append($('<img>',{src:'/photo/'+ image.data, alt: 'photo'}))

}



async function renderImages() {
    var ownedImages = await fetchOwnedImages();
    console.log(ownedImages.data[0]['filename']);
    console.log(ownedImages.data.length);

    for(var i = 0 ; i < ownedImages.data.length ; i++) {
        $('#photo-list').append($('<img>',{src:'/photo/'+ ownedImages.data[i]['filename'], alt: 'photo '+i}))
    }
}

class ImageList
{
    constructor($element) {
        this.$element = $element;
        this.images = [];
        this.render();
        $.ajax({
            url: this.$element.data('url')
        }).then(data => {
            this.references = data;
            this.render();
        })
    }
    render() {
        const itemsHtml = this.images.map(image => {
            return `
<li class="list-group-item d-flex justify-content-between align-items-center">
    ${image.originalFilename}
    <span>
        <img alt="photo" src="/admin/article/references/${image.id}/download"><span class="fa fa-download"></span></img>
    </span>
</li>
`
        });
        this.$element.html(itemsHtml.join(''));
    }
}





