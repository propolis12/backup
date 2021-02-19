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
//jquery_mousewheel($);
//malihu_custom_scrollbar_plugin($);
import axios from "axios";

//import {jquery_mousewheel} from "jquery-mousewheel";

//import {malihu_custom_scrollbar_plugin} from "malihu-custom-scrollbar-plugin";

createApp(MainPage).mount('#main')

/** to show the image filename in form field */
$('.custom-file-input').on('change', function (event) {
    var inputFile = event.currentTarget;
    $(inputFile).parent()
        .find('.custom-file-label')
        .html(inputFile.files[0].name);

})

var ownedImages;

$(document).ready(function() {


    /*$("#sidebar").mCustomScrollbar({
        theme: "minimal"
    });*/
    $('#sidebarCollapse').hide();
    $('.sidebar-header').on('click', function () {
        sidebarResponsive();
    });

    $('#sidebarCollapse').on('click', function () {
        sidebarResponsive();

    });
    // close dropdowns
    $('.collapse.in').toggleClass('in');
    // and also adjust aria-expanded attributes we use for the open/closed arrows
    // in our CSS
    $('a[aria-expanded=true]').attr('aria-expanded', 'false');


    $('#dropzone').hide();
    renderImages().then(r => response);

    initializeDropzone();
    let response;

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
    $('#photo-list').append($('<img>',{src:'/latest/photos/'+ names[i], alt: 'photo' , click:  function () { openWindow(this.src,37) }}))
    }
    ownedImages = await fetchOwnedImages();
}



async function renderImages() {
     ownedImages = await fetchOwnedImages();
    //console.log(ownedImages.data[0]['filename']);
    //console.log(ownedImages.data.length);
    console.log(ownedImages);
    for(var i = 0 ; i < ownedImages.data.length ; i++) {
        var parent = i;
        $('#photo-list').append($('<img>',{src:'/photo/'+ ownedImages.data[i]['originalName'], alt: 'photo '+i , click: function () { openWindow(this.src,29) } }))
    }
}



    $('#uploadIcon').click(function () {
            $('#dropzone').toggle();
    })

function sidebarResponsive() {
    $('#sidebar').toggleClass('active');
    $('#sidebar-wrapper').toggleClass('col-2');
    $('#sidebar-wrapper').toggleClass('col-0');
    $('#content-wrapper').toggleClass('col-10');
    $('#content-wrapper').toggleClass('col-12');
    if(!($('#sidebar').hasClass('active'))) {
        $('#sidebarCollapse').hide();
    } else {
        $('#sidebarCollapse').show();
    }
    //$('#collapseButton').toggleClass('fas fa-arrow-circle-right');
}


/** name - url of the image from source attribute
 * number - position of actual name in name(url) parameter
 * we want extract only the name , because the name(url) is not a good response for this request */
function openWindow(name , number) {
console.log(name.substr(number));
   window.open('/send/fullPhoto/' + name.substr(number));
    //'/send/fullPhoto/' + ownedImages.data[i]['filename'];


}
