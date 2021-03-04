import {insertTags} from "@/tag-services";

Dropzone .autoDiscover = false;
import {fetchLatestImages, fetchOwnedImages, deleteImage, fetchImages} from "@/services/images-service";
import {postAlbum, fetchAlbums, fetchAlbumImages, addToAlbum, deleteOnlyFromAlbum} from "@/album-services";
import './bootstrap';
import './styles/mainPage.css';
import dropzone from "dropzone";
import 'dropzone/dist/dropzone.css';
import $ from 'jquery';
import MainPage from "./pages/mainPage.vue";
import { createApp } from "vue";
//import "./bootstrap-tagsinput"
//import "./bootstrap-tags/js/bootstrap-tags.js"
require('bootstrap')


createApp(MainPage).mount('#main')

/** to show the image filename in form field */
$('.custom-file-input').on('change', function (event) {
    var inputFile = event.currentTarget;
    $(inputFile).parent()
        .find('.custom-file-label')
        .html(inputFile.files[0].name);

})

var albums = [];
var  selected  = [];
var allOwnedImages = [];
var currentNameSet;
var currentAlbum = '';
var ownedImages;
var currentImageIndex;
var myImages = [];

$(document).ready(async function() {
    //console.log(tags + "sdasfafadfsadfsadfsafsafsdfsfsdfsdfsdfscscscsdcs")
    myImages  = await fetchImages();
    console.log(myImages.data[6]["tags"][0]["name"]);
    $('#addTagLi').hide()
    $('#deleteOnlyFromAlbumLi').hide()
    $('#editNavbar').hide()
    allOwnedImages = await fetchOwnedImages()
    renderImages().then(r => {
        $('.thumbnailIcons').hide()
        setHoveringOverImages()
    });
    lazyLoading()

    $('#dropzone').hide();

    $('#fullscreenPicture').hide();

    $('#sidebarCollapse').hide();

    await loadAlbums();



    for (var i = 0 ; i < albums.data.length ; i++) {
        $('#addToAlbumDropdown').append('<a class="dropdown-item addToAlbumClickableLi" href="#">' +  albums.data[i]["name"] + ' </a>')
    }

    $('#sidebarCollapse').on('click', function () {
        sidebarResponsive();

    });
    $('#newAlbumLi').hide();
    // close dropdowns
    $('.collapse.in').toggleClass('in');
    // and also adjust aria-expanded attributes we use for the open/closed arrows
    // in our CSS
    $('a[aria-expanded=true]').attr('aria-expanded', 'false');

    setHoveringOverImages()


    initializeDropzone();
    $('body').css('pointer-events', 'all')

    /*$(document).on('keyup', '.tag-container input' , function (e) {
        if (e.key === 'Enter') {
            tags.push(input.value)
            addTags()
            input.value = ''
        }
    })*/
console.log(allOwnedImages)
});


$(document).on('click','.fa-chevron-circle-down' , async function () {
    console.log("klikol som na menu")

})

$(document).on('click', '.nav-item', function () {
    $(this).addClass('active')
    $('.nav-item').removeClass('active')
})

$(document).on('click', '#deleteOnlyFromAlbumLi', async function () {
    for (var i = 0; i < selected.length; i++ ) {
        await deleteOnlyFromAlbum(currentAlbum, selected[i])
    }
    reloadEditingTools()
    await renderImages()
})



$(document).on('click', '.addToAlbumClickableLi', async function () {
    var albumName = $(this).text()
    for (var i = 0; i < selected.length; i++ ) {
        await addToAlbum(albumName, selected[i])
    }
    reloadEditingTools()
    console.log(albumName)
})




$(document).on('click', '.fa-trash', async function () {
    console.log("klikol som")
    console.log($(this).parent().siblings().data()["name"])
    let imageName = $(this).parent().siblings().data()["name"]
    let response = await deleteImage(imageName)
    renderImages().then(r => {
    });
})



function setHoveringOverImages() {
    $('.thumbnailImage').mouseover(
        function () {
            $(this).siblings().show()
        }
    )

    $('.thumbnailImage').mouseout(
        function () {
            if($(this).siblings().children().hasClass('far')) {
                $(this).siblings().hide()
            }
        }
    )
    $('#editNavbar').mouseover(
        function () {
            $(this).css('opacity', 1)
        }
    )

    $('#editNavbar').mouseout(
        function () {
            $(this).css('opacity', 0.5)
        }
    )

    /*$('.thumbnailIcons').click(
        function () {
            $(this).siblings().mouseout(
                function () {
                    $(this).siblings().toggle()
                    //$(this).siblings().delay(2000).hide()
                }
            )

        })*/


}


/**
 * handling selecting photos
 */
$(document).on('click','.selectable', function () {
    $(this).toggleClass('far')
    $(this).toggleClass('fas')
    if($(".fas.fa-check-circle")[0]) {
        $('#editNavbar').show()
    } else {
        $('#editNavbar').hide()
    }
    var filenameToPush = $(this).parent().siblings().data('name')
    if($(this).hasClass('fas')) {
        selected.push(filenameToPush)
    } else {
        var index = selected.indexOf(filenameToPush)
        if (index !== -1 ) {
            selected.splice(index, 1)
        }
    }
    console.log($(this).parent().siblings().data('name'));
    console.log(selected.toString()  + " toto su selected items")
})






/**
 * delete selected photos
 */
$(document).on('click', '#deleteImagesNav' , async function () {
    if(selected.length > 0 ) {
        for (var i = 0 ; i < selected.length; i++) {
            await deleteImage(selected[i])
        }
        selected = []
        $('#editNavbar').hide()
        await renderImages()
    }
})


/*$(document).on('click','.far', function () {
    $(this).parent().show()
})

$(document).on('click','.fas', function () {
    $(this).parent().hide()
})*/

function initializeDropzone() {
    var newNames = [];
    var formElement = document.querySelector('.js-reference-dropzone');
    if (!formElement) {
        return;
    }
    var dropzone = new Dropzone(formElement, {
        paramName: 'dropzone',
        acceptedFiles: ".jpeg,.jpg,.png,.gif",
        params: {'albumName': currentAlbum },
        renameFile: function (file) {
            let newName = new Date().getTime() + '_' + file.name;
             newNames.push(newName);
             return newName;
        },
        init: function() {
            this.on('sending', function (file,xhr, data) {
                data.append("data" , currentAlbum);
                console.log(data)
            })

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
                if (currentAlbum !== '') {

                }
                    setTimeout(() => $('#dropzone').html(""),2000)

                });
            })
        }
    });
}


async function appendImage(names) {
    currentNameSet.push(names)
    for(let i = 0; i < names.length; i++) {
    console.log(names[i]);
    $('#photo-list').append('<div id=' + (currentNameSet.length + i)  + '  class="thumbnailDiv" ><div class="thumbnailIcons" ><i class="far fa-check-circle fa-2x selectable"></i></div></div>')
    $('#' + (currentNameSet.length + i)).append($('<img>',{src:'/latest/photos/'+ names[i], alt: '' ,'data-name': names[i] , click:  function () { openWindow(this.dataset.name, currentNameSet) } , class:'thumbnailImage' , loading: 'lazy'}))
    }
    ownedImages = await fetchOwnedImages();

    $('.thumbnailIcons').hide()
    setHoveringOverImages()
}



async function renderImages() {

    $('#photo-list').html('');
    var albumImages = []
    var ownedImagesNames = []
    if (currentAlbum === '') {
        ownedImages = await fetchOwnedImages();
        for (var i = 0 ; i  < ownedImages.data.length; i++) {
            ownedImagesNames[i] = ownedImages.data[i]['originalName']
        }
        currentNameSet = ownedImagesNames

    } else {
        albumImages = await fetchAlbumImages(currentAlbum)
        for (var i = 0 ; i < albumImages.data[0].length ; i++ ){
            ownedImagesNames[i] = albumImages.data[0][i]['originalName']
        }
        //var imageNames = images.data[0][]['originalname']
        currentNameSet = ownedImagesNames
    }

    // ownedImages = await fetchOwnedImages();

    console.log(ownedImagesNames + "toto je ownedImages");
    for(var i = 0 ; i < ownedImagesNames.length ; i++) {
        if (i > 30) {
            /** for lazy loading */
            $('#photo-list').append('<div id=' + i + ' class="thumbnailDiv"><div class="thumbnailIcons" ><i class="far fa-check-circle fa-2x selectable"></i></div> </div>')
            $('#' + i).append($('<img>',{ realsrc:'/photo/'+ ownedImagesNames[i], src:'', alt: '' , 'data-name': ownedImagesNames[i] , click:  function () { openWindow(this.dataset.name, ownedImagesNames) } , class:'thumbnailImage'}))
            continue
        }
        var parent = i;

        $('#photo-list').append('<div id=' + i + ' class="thumbnailDiv"><div class="thumbnailIcons" ><i class="far fa-check-circle fa-2x selectable"></i></div> </div>')
        $('#' + i).append( $('<img>',{ realsrc:'/photo/'+ ownedImagesNames[i], src:'/photo/'+ ownedImagesNames[i], alt: '' , 'data-name': ownedImagesNames[i] , click:  function () { openWindow(this.dataset.name, ownedImagesNames) } , class:'thumbnailImage'} ))

    }
    $('.thumbnailIcons').hide()
    setHoveringOverImages()
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


/** Full photo controls
 *  name - url of the image from source attribute
 * number - position of actual name in name(url) parameter
 * we want extract only the name , because the name(url) is not a good response for this request */
function openWindow(name, nameSet) {
    currentNameSet = nameSet
    //console.log(name + " toto je manommsddfsffsdf");
    //var name = name.substr(number)
    $('#insertPicture').html($('<img>',{src:'send/fullPhoto/'+ name, alt: '' , id: 'fullScreenImage'}));
    $('#fullscreenPicture').show();
    $('#photo-list').hide();
    findCurrentImage(name, nameSet);



}

function closeImage() {
    console.log("blaaaaaaaaaaaaaaaaa");
    $('#fullscreenPicture').hide();
    $('#photo-list').show();
}


function findCurrentImage(name , nameSet) {
    console.log(name + " toto je meno image");

    for(var i = 0; i < nameSet.length; i++) {
        console.log(nameSet[i] + " toto je meno v nameset")
        //console.log(nameSet.data[i][0]["originalName"] + " toto je nameset in album");
        if (nameSet[i] == name ) {
            console.log(nameSet[i]);
            currentImageIndex = i;
            console.log(currentImageIndex + "toto je current index");
        }
    }

}
function previousImage() {
    console.log(currentImageIndex + " teraz je toto index")
    if(currentImageIndex !== 0) {
        currentImageIndex--;
        $('#insertPicture').html($('<img>', {
            src: 'send/fullPhoto/' + currentNameSet[currentImageIndex],
            alt: 'photo',
            id: 'fullScreenImage'
        }));

        console.log(currentImageIndex + "index po kliknuti");
    }

}

function nextImage() {
    if(currentImageIndex !== (currentNameSet.length - 1)) {
        currentImageIndex++;
        $('#insertPicture').html($('<img>',{src:'send/fullPhoto/'+ currentNameSet[currentImageIndex], alt: '' , id: 'fullScreenImage'}));
        console.log(currentImageIndex + "index po kliknuti");
    }
}


$('#closeFullScreenSpan').on('click', function () {
    closeImage();

});

$('#navigateLeftSpan').on('click', function () {
    previousImage();
})

$('#navigateRightSpan').on('click', function () {
    nextImage();
})







/** -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 * Album logic
 *
 * */

$('#createAlbumLi').on('click', function () {
    $('#newAlbumLi').toggle()
})

$('#createAlbumInputButton').on('click', async function () {
    //console.log($('#albumNameTextInput').val());
    let response = await postAlbum($('#albumNameTextInput').val());
    console.log("response is " + response.data);
    await loadAlbums();
})


async function listAlbumImages(name) {
    var images = await fetchAlbumImages(name);
    //console.log(images)
    return images
}


/** listener on album click*/
$(document).on('click',"a.AlbumListItem" , async function () {

    $('#photo-list').html('');
    $('#deleteOnlyFromAlbumLi').show()
    var albumName = $(this).data("albumname");
    $('#sidebarHeader').text(albumName)
    currentAlbum = albumName;
    $('#nav-brandEditNavbar').text(albumName)
    reloadEditingTools()
    await renderImages();

    //$('#hiddenDropzoneInput').val(currentAlbum)
    /*console.log(albumName + "toto je album name");
    var images = await listAlbumImages(albumName)
    console.log(images.data[0][0]['originalName'] + "toto su images albumove");
    console.log(images.data[0].length + "toto je dlzka");
    var imageNames = []
    for (var i = 0 ; i < images.data[0].length ; i++ ){
        imageNames[i] = images.data[0][i]['originalName']
    }
    //var imageNames = images.data[0][]['originalname']
    currentNameSet = imageNames
    console.log(currentNameSet + " toto je current Nameset")
    if(images.data[0].length > 1) {
       // console.log(images.data[0].length + "dlzka")

        //$('#photo-list').html($('<img>',{src:'/photo/'+ images.data[0][0]['originalName'], alt: '' , dataName: images.data[0][0]['originalName'] , click:  function () { openWindow(this.dataName) } , class:'thumbnailImage', loading: 'lazy'}))
        for (var i = 0; i < images.data[0].length ; i++) {
            console.log(i)
            $('#photo-list').append('<div id=' + i + ' class="thumbnailDiv"><div class="thumbnailIcons" ><i class="fas fa-trash fa-2x"></i></div> </div>')
            $('#' + i).append( $('<img>',{ realsrc:'/photo/'+ imageNames[i], src:'/photo/'+ imageNames[i], alt: '' , 'data-name': imageNames[i] , click:  function () { openWindow(this.dataset.name, imageNames) } , class:'thumbnailImage'} ))


            //$('#photo-list').append($('<img>',{   src:'/photo/'+ images.data[0][i]['originalName'], alt: '' , dataName: images.data[0][i]['originalName'] , click:  function () { openWindow(this.dataName) } , class:'thumbnailImage', loading: 'lazy'}))
        }

    }
    $('.thumbnailIcons').hide()
    setHoveringOverImages()

    console.log(currentAlbum + " toto je currentAlbum")*/
})





async function loadAlbums() {
    albums = await fetchAlbums();
    for (var i = 0; i < albums.data.length; i++) {
        console.log(albums.data[i]["name"])
        $('#homeSubmenu').append('<li class="albumListItem"><a class="AlbumListItem" data-AlbumName="' + albums.data[i]["name"] + '"> ' + albums.data[i]["name"] + '</a></li>')

    }

}

$('#loadAllImagesLi').on('click', async function () {
    currentAlbum = '';
    $('#deleteOnlyFromAlbumLi').hide()
    $('#nav-brandEditNavbar').text("All images")
    $('#sidebarHeader').text("All Images")
    reloadEditingTools()
    await renderImages()

})

$('#photo-list').on('click', function () {
    $('.dropzone').hide()
})



function lazyLoading() {
    var refresh_handler = function(e) {
        var elements = document.querySelectorAll("*[realsrc]");
        for (var i = 0; i < elements.length; i++) {
            var boundingClientRect = elements[i].getBoundingClientRect();
            if (elements[i].hasAttribute("realsrc") && boundingClientRect.top < window.innerHeight) {
                elements[i].setAttribute("src", elements[i].getAttribute("realsrc"));
                elements[i].removeAttribute("realsrc");
            }
        }
    };

    window.addEventListener('scroll', refresh_handler);
    window.addEventListener('load', refresh_handler);
    window.addEventListener('resize', refresh_handler);
}


/**
 * to reset edit navbar data
 */
function reloadEditingTools() {
    selected = []
    $('.fas.fa-check-circle').addClass('far')
    $('.fas.fa-check-circle').removeClass('fas')
    $('#editNavbar').hide()
    $('.dropzone').hide()
}

/*$(document).on('submit', '.bootstrap-tagsinput input' , function () {
    $(this).prepend('<span class="x">x</span>')
})*/

/*$(function () {
    $('.tag.label.label-info').prepend('<span class="x">x</span>')
})*/



/** hint pre tagy */


/*var colors = ["red", "blue", "green", "yellow", "brown", "black"];

 $('#tags').tagsinput({
  typeahead: {
    source: function(query) {
      return colors
    }
  }
});*/

/**
 *
 * logic for tags
 */

const tagContainer = document.querySelector('#tag-container');
const inputTagsSearch = document.querySelector('#tag-container input');

const tagContainerEdit = document.querySelector('#forTags')
const inputTagEdit = document.querySelector('#addTagInput')

let tags = [];
let inpuTags = []

function createTag(label, classname, attributename) {
    //console.log(tags + "toto su tags")
    const div = document.createElement('div');
    div.setAttribute('class', attributename);
    const span = document.createElement('span');
    span.innerHTML = label;
    const closeIcon = document.createElement('i');
    //closeIcon.innerHTML = 'x';
    closeIcon.setAttribute('class', 'fas fa-times ' + classname);
    closeIcon.setAttribute('data-item', label);
    div.appendChild(span);
    div.appendChild(closeIcon);
    return div;
}

function clearTags(classname) {
    document.querySelectorAll(classname).forEach(tag => {
        tag.parentElement.removeChild(tag);
    });
}

function addTags() {

    clearTags('.tag');
    tags.slice().reverse().forEach(tag => {
        tagContainer.prepend(createTag(tag, 'IconTagSearch', 'tag'));
    });
}


function addTagsInput() {

    clearTags('.inputTag');
    inpuTags.slice().reverse().forEach(tag => {
        tagContainerEdit.append(createTag(tag, 'IconTagInput', 'inputTag'));
    });
}



inputTagsSearch.addEventListener('keyup', (e) => {
    if (e.key === 'Enter') {
        console.log(tags + "toto su tags")
        console.log(e.target.value)
            console.log(e.target.id)
            e.target.value.split(',').forEach(tag => {
                if (tag !== '') {
                    tags.push(tag);
                    console.log("sadasdadadasfasfa")
                }
            });
            console.log(tags)
            addTags();
            inputTagsSearch.value = '';
    }
});


inputTagEdit.addEventListener('keyup', (e) => {
    if (e.key === 'Enter') {
        console.log(tags + "toto su tags edit ")
        console.log(e.target.value)
            console.log(e.target.id)
            e.target.value.split(',').forEach(tag => {
                if (tag !== '') {
                    inpuTags.push(tag);
                    console.log("sadasdadadasfasfa")
                }
            });
            console.log(tags)
            addTagsInput();
            inputTagEdit.value = '';
    }
});








document.addEventListener('click', (e) => {
    console.log(e.target.tagName);
    if (e.target.classList.contains('IconTagSearch')) {
        console.log("klikol som input")
        const tagLabel = e.target.getAttribute('data-item');
        const index = tags.indexOf(tagLabel);
        tags = [...tags.slice(0, index), ...tags.slice(index+1)];

    }
    addTags();
})

document.addEventListener('click', (e) => {
    console.log(e.target.tagName);
    if (e.target.classList.contains('IconTagInput')) {
        console.log("klikol som input")
        const tagLabel = e.target.getAttribute('data-item');
        const index = inpuTags.indexOf(tagLabel);
        inpuTags = [...inpuTags.slice(0, index), ...inpuTags.slice(index+1)];

    }
    addTagsInput();
})

inputTagsSearch.focus();
inputTagEdit.focus();


/**
 * listeners for tags
 */

$(document).on('click','#searchTagsButton', function () {

})

$(document).on('click','#addTag', async function () {
    $('#addTagLi').toggle()

})


/**
 * -------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 * tags button listeners
 */

$(document).on('click', '#addTagButton' , async function () {
    for (var i = 0 ; i < selected.length; i++) {
        await insertTags(selected[i], inpuTags)
    }
    inpuTags = []
    $('#forTags').html('')
    console.log(inpuTags + " toto su tags")
    reloadEditingTools()

})

$(document).on('click', '#searchTagsButton', async function() {


})






$(document).on('click','#closeNavbar', function () {
    $('#editNavbar').hide()
})