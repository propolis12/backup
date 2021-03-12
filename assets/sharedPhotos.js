import {
    fetchLatestImages,
    fetchOwnedImages,
    deleteImage,
    fetchImages,
    getImageInfo,
    makePublic, makePrivate, fetchPublicImages
} from "@/services/images-service";

import './bootstrap';
import $ from 'jquery';
import {fetchAlbumImages} from "@/services/album-services";
import {setHoveringOverImages} from "@/mainPage";
//require('bootstrap')

var publicImages = []

$(document).ready(async function() {
    $('#uploadIcon').remove()
    console.log("asdafsfsdfsdfsdfsfsdfsdfsdf")
    publicImages = await fetchPublicImages()
    console.log(publicImages)
    await renderImagesShared()
    $('#photo-list').html('')
})




async function renderImagesShared() {


    $('#photoListShared').html('');
    var publicImagesNames = []

        publicImages = await fetchPublicImages();
        for (var i = 0 ; i  < publicImages.data.length; i++) {
            publicImagesNames[i] = publicImages.data[i]['originalName']
        }

    // ownedImages = await fetchOwnedImages();

    console.log(publicImagesNames + "toto je ownedImages");
    for(var i = 0 ; i < publicImages.data.length ; i++) {

        if (i > 30) {
            /** for lazy loading */
            $('#photoListShared').append('<div id=' + i + 'shared' + ' class="thumbnailDivShared"><div class="thumbnailIconsShared" ><i class="far fa-heart fa-2x"></i></div> </div>')
            if (publicImages.data[i]['publishedAt'] !== null) {
                $('#' + i + 'shared').append('<div class="publicInfoDiv"><div class="ownerNameDiv">'+ publicImages.data[i]['username']  + '</div><div class="publishDateDiv">'+ timeSince(new Date( (publicImages.data[i]['publishedAt'])))  + '</div></div>')
            } else {
                $('#' + i + 'shared').append('<div class="publicInfoDiv"><div class="ownerNameDiv">'+ publicImages.data[i]['username']  + '</div></div>')
            }

            $('#' + i + 'shared').append($('<img>',{ realsrc:'/photo/'+  publicImages.data[i]['originalName'], src:'', alt: '' , 'data-name':  publicImages.data[i]['originalName'] , class:'thumbnailImageShared'}))
            continue
        }

        $('#photoListShared').append('<div id=' + i + 'shared' + ' class="thumbnailDivShared"><div class="thumbnailIconsShared" ><i class="far fa-heart fa-2x"></i></div> </div>')
        if (publicImages.data[i]['publishedAt'] !== null) {
            $('#' + i + 'shared').append('<div class="publicInfoDiv"><div class="ownerNameDiv">'+ publicImages.data[i]['username']  + '</div><div class="publishDateDiv">'+ timeSince(new Date( (publicImages.data[i]['publishedAt'])))  + '</div></div>')
        } else {
            $('#' + i + 'shared').append('<div class="publicInfoDiv"><div class="ownerNameDiv">'+ publicImages.data[i]['username']  + '</div></div>')
        }

        $('#' + i + 'shared').append( $('<img>',{ realsrc:'/photo/'+  publicImages.data[i]['originalName'], src:'/photo/'+  publicImages.data[i]['originalName'], alt: '' , 'data-name': publicImages.data[i]['originalName'], class:'thumbnailImageShared'} ))

    }
    $('.thumbnailIcons').hide()
    //setHoveringOverImages()
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