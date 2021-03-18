import {
    fetchLatestImages,
    fetchOwnedImages,
    deleteImage,
    fetchImages,
    getImageInfo,
    makePublic, makePrivate, fetchPublicImages, likePhoto, fetchLikedImages, unlikePhoto
} from "@/services/images-service";

import './bootstrap';
import $ from 'jquery';
import {fetchAlbumImages} from "@/services/album-services";
import {openWindow, setHoveringOverImages,nextImage,findCurrentImage,previousImage,closeImage} from "@/mainPage";
//require('bootstrap')

var publicImages = []
var likedImages = []
var publicImagesNames = []
$(document).ready(async function() {
    $('#uploadIcon').remove()
    console.log("asdafsfsdfsdfsdfsfsdfsdfsdf")
    publicImages = await fetchPublicImages()
    likedImages = await fetchLikedImages()
    console.log(publicImages)
    console.log("toto budu moje liked images")
    console.log(likedImages)
    await renderImagesShared()
    $('#photo-list').html('')
    for (var i = 0; i < publicImages.data.length; i++) {
        publicImagesNames[i] = publicImages.data[i]['originalName']
    }
})




async function renderImagesShared() {


    $('#photoListShared').html('');
    var iconClass = 'far'
    publicImages = await fetchPublicImages();


    // ownedImages = await fetchOwnedImages();

    console.log(publicImagesNames + "toto je ownedImages");
    for (var i = 0; i < publicImages.data.length; i++) {


        /** for lazy loading */
        renderHelp(i)
        if (i > 30) {
            $('#' + i + 'shared').append($('<img>', {
                realsrc: '/public/photo/' + publicImages.data[i]['originalName'],
                src: '',
                alt: '',
                'data-name': publicImages.data[i]['originalName'],
                class: 'thumbnailImageShared'
            }))
        } else {
            $('#' + i + 'shared').append($('<img>', {
                realsrc: '/public/photo/' + publicImages.data[i]['originalName'],
                src: '/public/photo/' + publicImages.data[i]['originalName'],
                alt: '',
                'data-name': publicImages.data[i]['originalName'],
                class: 'thumbnailImageShared'
            }))
        }
        for (var l = 0; l < likedImages.data.length; l++) {
            if (likedImages.data[l]["originalName"] === publicImages.data[i]["originalName"]) {
                iconClass = 'fas'
                break
            }
        }

        $('#' + i + 'shared').append('<div class="thumbnailIconsShared" ><span class="numberLikes mr-2">' + publicImages.data[i]['likes'].length + '</span><i class="' + iconClass + ' fa-heart  likeable" ></i></div>')
        iconClass = 'far'

    }
}


function renderHelp(i) {
    $('#photoListShared').append('<div id=' + i + 'shared' + ' class="thumbnailDivShared"> </div>')
    if (publicImages.data[i]['publishedAt'] !== null) {
        $('#' + i + 'shared').append('<div class="publicInfoDiv"><div class="ownerNameDiv">'+ publicImages.data[i]['username']  + '</div><div class="publishDateDiv">'+ timeSince(new Date( (publicImages.data[i]['publishedAt'])))  + '</div></div>')
    } else {
        $('#' + i + 'shared').append('<div class="publicInfoDiv"><div class="ownerNameDiv">'+ publicImages.data[i]['username']  + '</div></div>')
    }
}


function timeSince(date) {

    var seconds = Math.floor((new Date() - date) / 1000);

    var interval = seconds / 31536000;

    if (interval > 1) {
        return Math.floor(interval) + " years";
    }
    interval = seconds / 2592000;
    if (interval > 1) {
        return Math.floor(interval) + " months";
    }
    interval = seconds / 86400;
    if (interval > 1) {
        return Math.floor(interval) + " days";
    }
    interval = seconds / 3600;
    if (interval > 1) {
        return Math.floor(interval) + " hours";
    }
    interval = seconds / 60;
    if (interval > 1) {
        return Math.floor(interval) + " minutes";
    }
    return Math.floor(seconds) + " seconds";
}

$(document).on('click','.likeable', async function () {
    $(this).toggleClass('far')
    $(this).toggleClass('fas')
    var liked = false
    var image
    var name = $(this).parent().siblings()[1].dataset.name
    //console.log(name)
    console.log(likedImages.data.length)
    for (i = 0 ; i < likedImages.data.length; i++) {
        console.log(likedImages.data[i]["originalName"] + "asdafsssssssssssasfaaaaasfdsssssssssssssssssssssssssff")
        if (likedImages.data[i]["originalName"] === name) {
            liked = true
        }
    }
    if(!liked) {
        image = await likePhoto(name)
        //console.log(image)
        //console.log(likedImages)
       likedImages.data.push(image.data)
        toggleLikedHeart()
    } else {
       image = await unlikePhoto(name)
        //console.log(image)
        var index = likedImages.data.indexOf(name)
        likedImages.data.splice(index , 1)
        //console.log("likedImages po delete ")
        //console.log(likedImages)
        toggleLikedHeart()


    }


    if( image.status !== 500) {
        $(this).siblings().text((image.data["likes"].length).toString())
    }
   // console.log(image)
    //var index = publicImages.indexOf(image)
    for (var i = 0; i < publicImages.data.length ; i++) {
        if(image["originalName"] === publicImages.data[i]["originalName"]) {
            publicImages.data[i] = image

        }
    }

    //var pos = publicImages.map(function(e) { return e["originalName"]; }).indexOf('stevie');
   // console.log(index);
    console.log(publicImages)
})


function toggleLikedHeart() {
    $(this).toggleClass('far')
    $(this).toggleClass('fas')
}

$(document).on('click', '.thumbnailImageShared' , async  function () {
        console.log($(this).data('name'))
        var imageName = $(this).data('name')
        openWindow(imageName, publicImagesNames)
        $('#fullscreenPicture').prepend('<div></div>')

})