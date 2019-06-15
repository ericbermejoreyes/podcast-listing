lapi = (function (...args) {
    const API_KEY_HEADER = 'X-ListenAPI-Key';
    const BASE_URI = 'https://listen-api.listennotes.com/api/v2';

    let api = this;
    let apiKey = args[0];

    api.getKey = function () {
       return apiKey;
    };

    api.getGenres = callback => {
        $.ajax({
            url: BASE_URI + '/genres',
            headers: {
                [API_KEY_HEADER]: apiKey
            },
            success: data => {
                callback(data);
            }
        });
    };

    api.getPodcastsByGenre = (o, callback) => {
        $.ajax({
            url: BASE_URI + '/best_podcasts' + serialize(o),
            headers: {
                [API_KEY_HEADER]: apiKey
            },
            success: data => {
                callback(data);
            }
        });
    };

    function serialize(array) {
        let string = [];
        for (let i in array) {
            string.push(i + '=' + encodeURI(array[i]));
        }
        return '?' + string.join('&');
    }

    return api;
}(LAPIKEY));