import axios from "axios";

export async function postAlbum(name) {
    return await axios.post('/album/create', {
        "albumName": name,
    }, {
        headers: {
            'Content-Type': 'application/json'
        }
    });

}

export async function fetchAlbums() {
    return await axios.get('/fetch/albums')

}

export async function fetchAlbumImages(name) {
    return await axios.get('/fetch/album/images/' + name)

}

export async function addToAlbum(albumName, filename) {
    return await axios.post('/add/to/album/' + albumName , {
        "filename": filename,
    },
        {
            headers: {
                'Content-Type': 'application/json'
            }
        });
}

export async function deleteOnlyFromAlbum(albumName , filename) {
    return await axios.post('/remove/from/album/' + albumName , {
            "filename": filename,
        },
        {
            headers: {
                'Content-Type': 'application/json'
            }
        });
}

export async function deleteAlbum(name) {
    return await axios.delete('/delete/album/' + name)
}