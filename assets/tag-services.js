import axios from "axios";

export async function insertTags(imageName, tags) {
    return await axios.post('/add/tags/' + imageName, {
            "tags": tags,
        },
        {
            headers: {
                'Content-Type': 'application/json'
            }
        });
}