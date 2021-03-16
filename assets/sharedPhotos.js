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
import {setHoveringOverImages} from "@/mainPage";
//require('bootstrap')

var publicImages = []
var likedImages = []
$(document).ready(async function() {
    $('#uploadIcon').remove()
    console.log("asdafsfsdfsdfsdfsfsdfsdfsdf")
    publicImages = await fetchPublicImages()
    likedImages = await fetchLikedImages()
    console.log(publicImages)
    console.log("toto budu moje liked images")
    console.log(likedImages.data)
    await renderImagesShared()
    $('#photo-list').html('')
})




async function renderImagesShared() {


    $('#photoListShared').html('');
    var publicImagesNames = []
    var iconClass = 'far'
        publicImages = await fetchPublicImages();
        for (var i = 0 ; i  < publicImages.data.length; i++) {
            publicImagesNames[i] = publicImages.data[i]['originalName']
        }

    // ownedImages = await fetchOwnedImages();

    console.log(publicImagesNames + "toto je ownedImages");
    for(var i = 0 ; i < publicImages.data.length ; i++) {

        if (i > 30) {
            /** for lazy loading */
            $('#photoListShared').append('<div id=' + i + 'shared' + ' class="thumbnailDivShared"><div class="thumbnailIconsShared" ><span class="numberLikes">3</span><i class="far fa-heart  likeable"></i></div> </div>')
            if (publicImages.data[i]['publishedAt'] !== null) {
                $('#' + i + 'shared').append('<div class="publicInfoDiv"><div class="ownerNameDiv">'+ publicImages.data[i]['username']  + '</div><div class="publishDateDiv">'+ timeSince(new Date( (publicImages.data[i]['publishedAt'])))  + '</div></div>')
            } else {
                $('#' + i + 'shared').append('<div class="publicInfoDiv"><div class="ownerNameDiv">'+ publicImages.data[i]['username']  + '</div></div>')
            }

            $('#' + i + 'shared').append($('<img>',{ realsrc:'/photo/'+  publicImages.data[i]['originalName'], src:'', alt: '' , 'data-name':  publicImages.data[i]['originalName'] , class:'thumbnailImageShared'}))


                for (var l = 0 ; l < likedImages.data.length; l++) {
                    if (likedImages.data[l] === publicImages.data[j]["originalName"]) {
                        iconClass = 'fas'
                        break;
                    }
                }

            $('#' + i + 'shared').append('<div class="thumbnailIconsShared" ><span class="numberLikes mr-2">' + publicImages.data[i]['likes'].length + '</span><i class="'+ iconClass + 'fa-heart  likeable" ></i></div>')
            iconClass = 'far'
            continue
        }

        $('#photoListShared').append('<div id=' + i + 'shared' + ' class="thumbnailDivShared"> </div>')
        if (publicImages.data[i]['publishedAt'] !== null) {
            $('#' + i + 'shared').append('<div class="publicInfoDiv"><div class="ownerNameDiv">'+ publicImages.data[i]['username']  + '</div><div class="publishDateDiv">'+ timeSince(new Date( (publicImages.data[i]['publishedAt'])))  + '</div></div>')
        } else {
            $('#' + i + 'shared').append('<div class="publicInfoDiv"><div class="ownerNameDiv">'+ publicImages.data[i]['username']  + '</div></div>')
        }

        $('#' + i + 'shared').append( $('<img>',{ realsrc:'/photo/'+  publicImages.data[i]['originalName'], src:'/photo/'+  publicImages.data[i]['originalName'], alt: '' , 'data-name': publicImages.data[i]['originalName'], class:'thumbnailImageShared'} ))
        console.log("----------------------------------------------------------------------------")
            console.log(likedImages.data.length)
            for (var l = 0 ; l < likedImages.data.length; l++) {
                //console.log(likedImages[l]["originalName"])
                //console.log(likedImages[l]["originalName"]+ '  ' + publicImages.data[j]["originalName"])
                if (likedImages.data[l]["originalName"] === publicImages.data[i]["originalName"]) {
                    iconClass = 'fas'
                    console.log("zhoda")
                    break
                }
            }

        $('#' + i + 'shared').append('<div class="thumbnailIconsShared" ><span class="numberLikes mr-2">' + publicImages.data[i]['likes'].length + '</span><i class="'+ iconClass + ' fa-heart  likeable"></i></div>')
        iconClass = 'far'
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

$(document).on('click','.likeable', async function () {
    $(this).toggleClass('far')
    $(this).toggleClass('fas')
    var liked = false
    var image
    var name = $(this).parent().siblings()[1].dataset.name
    //console.log(name)
    console.log(likedImages)
    for (i = 0 ; i < likedImages.data.length; i++) {
        //console.log(likedImages.data[i]["originalName"])
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

})