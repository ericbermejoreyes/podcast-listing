function Note() {
    const REQUEST_HEADERS = {
        'X-RapidAPI-Key': L_API_KEY,
        'X-Requested-With': 'XMLHttpRequest'
    };

    this.getGenres = function () {
        let request = new XMLHttpRequest();

        request.open('GET', 'https://api.listennotes.com/api/v1/genres');
        setRequestHeaders(request);

        return getResponse(request, function (response) {
            return response.genres;
        });
    };

    this.getPodcastsByGenre = function (params) {
        let request = new XMLHttpRequest();

        request.open('GET', 'https://api.listennotes.com/api/v1/best_podcasts?' + paramEncode(params));
        setRequestHeaders(request, REQUEST_HEADERS);

        return getResponse(request);
    };

    function setRequestHeaders(request) {
        for (let header in REQUEST_HEADERS) {
            request.setRequestHeader(header, REQUEST_HEADERS[header]);
        }
    }

    // return a Promise containing the response of the request
    function getResponse(request, callback) {
        return new Promise(function (resolve, reject) {
            request.onreadystatechange = function() {
                if (this.readyState === 4 && this.status === 200) {
                    let response = JSON.parse(request.responseText);
                    if (callback) {
                        resolve(callback(response));
                    } else {
                        resolve(response);
                    }
                } else if (this.readyState === 4 && this.status !== 200) {
                    console.log('error: ' + request.responseText);
                    reject(request.responseText);
                }
            };

            request.send();
        });
    }

    function paramEncode(params) {
        let encodedParams = [];
        for (let i in params) {
            encodedParams.push(i + '=' + encodeURI(params[i]));
        }
        return encodedParams.join('&');
    }
}