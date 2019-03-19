/**
 *  On load, called to load the auth2 library and API client library.
 */
function handleClientLoad() {
    const authorization = document.getElementById('spreadsheet-authorization')
    const authorizeButton = document.getElementById('authorize-button');
    const loadingScreen = document.getElementById('loading');

    let processing = false;

    let note = new Note();

    window.addEventListener("beforeunload", function(event) {
        if (processing) {
            event.returnValue = "You are leaving the page, data generation will not continue";
        }
    });

    // create an instance of the spreadsheet
    let spreadsheet = new Spreadsheet(function (status) {
        if (status.signedIn) {
            init()
                .then(function () {
                    spreadsheet
                        .getInfo()
                        .then(function (info) {
                            let sheets = info.sheets;

                            for (let i in sheets) {
                                sheets[i] = new Sheet(sheets[i], true);

                                // delete default sheet of spreadsheet
                                if (sheets[i].properties.title.toLowerCase().indexOf('sheet') >= 0 && sheets.length > 1) {
                                    sheets[i].delete();
                                    delete sheets[i];
                                }
                            }

                            note
                                .getGenres()
                                .then(function (genres) {
                                    loadGui({genres})
                                        .then(function () {
                                            initFormControls(sheets, genres);
                                        });
                                });
                        });
                });
        } else {
            loadingScreen.style.display = 'none';
        }
    });

    authorizeButton.onclick = spreadsheet.signIn;

    // load the GUI
    function loadGui(data) {
        let request = new XMLHttpRequest();
        request.open('POST', 'html/gui');
        setRequestHeaders(request, {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        });

        return new Promise(function (resolve) {
            request.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    let content = document.getElementById('content');
                    content.innerHTML = this.responseText;
                    resolve();
                }
            };

            request.send(JSON.stringify(data));
        });
    }

    function pushSpreadsheetId(spreadsheetId) {
        let request = new XMLHttpRequest();
        request.open('POST', 'spreadsheet/save?spreadsheet_id=' + encodeURI(spreadsheetId));
        setRequestHeaders(request, {'X-Requested-With': 'XMLHttpRequest'});

        return new Promise(function (resolve) {
            request.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    resolve(this.responseText);
                }
            };

            request.send();
        });
    }

    // initiate form controls
    function initFormControls(sheets, genres) {
        loadingScreen.style.display = 'none';

        // bind click event to signout button
        const signOutButton = document.getElementById('signout-button');

        signOutButton.onclick = spreadsheet.signOut;

        const form = document.getElementById('podcast-request');
        const genreSelect = document.querySelector('#podcast-request select[name="genre_id"]');
        const genreNameInput = document.querySelector('#podcast-request input[name="genre_name"]');
        const capsule = document.querySelector('#podcast-request div.capsule');

        genreSelect.onchange = function () {
            let selected = document.querySelector('#podcast-request option[value="' + this.value + '"]');
            genreNameInput.value = selected.innerText;
        };

        // submit request
        form.onsubmit = function (e) {
            e.preventDefault();
            processing = true;
            hideErrorMessage();
            let miniLoad = document.querySelector('.mini-load');
            let urlLink = document.querySelector('a#url');
            let urlOutput = document.getElementById('url-output');

            miniLoad.style.display = 'block';
            urlOutput.style.display = 'none';
            capsule.style.display = 'none';

            let formData = serialize(form);
            // let categoryName = formData.genre_name;

            let genreList = [];
            if (parseInt(formData['genre_id'], 10) === -1) {
                for (let i in genres) {
                    genreList.push({genre_id: genres[i].id, genre_name: genres[i].name, safe_mode: formData.safe_mode, page: 1});
                }
            } else {
                formData.page = 1;
                genreList.push(formData);
            }

            repeatUntilEnd(genreList, function (request, $next) {
                let categoryName = request.genre_name;
                delete request.genre_name;

                getSheet(categoryName, sheets).then(function (sheet) {
                    // clear the sheet contents before writing;
                    sheet
                        .clear('A2:D')
                        .then(function () {
                            getPodcastsByGenre(request,
                                function (podcasts, startIndex) {
                                    // write each podcasts for each page in spreadsheet
                                    for (let i in sheets) {
                                        // delete default sheet of spreadsheet
                                        if (sheets[i].properties.title.toLowerCase().indexOf('sheet') >= 0 && sheets.length > 1) {
                                            sheets[i].delete();
                                            delete sheets[i];
                                        }
                                    }

                                    sheet
                                        .update('A' + startIndex + ':D', podcasts)
                                        .catch(function () {
                                            showErrorMessage(categoryName);
                                        });

                                },
                                function () {
                                    // fix the cell size once all data are written
                                    sheet
                                        .fixCellSize()
                                        .then(function () {
                                            $next();
                                        })
                                        .catch(function () {
                                            showErrorMessage(categoryName);
                                        });
                                });
                        }).catch(function () {
                            showErrorMessage(categoryName);
                        });
                });
            }, function () {
                miniLoad.style.display = 'none';
                capsule.style.display = 'flex';
                urlOutput.style.display = 'block';
                processing = false;

            });

            return false;
        }
    }

    function repeatUntilEnd(array, callback, endCallback) {
        if (array.length) {
            let data = array.pop();

            callback(data, function () {
                repeatUntilEnd(array, callback, endCallback);
            });
        } else {
            if (endCallback) {
                endCallback();
            }
        }
    }

    // use to serialize form data into url encoded array object
    function serialize(form) {
        let length = form.length;
        let data = {};

        for (let i = 0; i < length; i++) {
            let field = form[i];
            if (field.name !== '') {
                data[field.name] = field.value;
            }
        }

        return data;
    }

    function setRequestHeaders(request, headers) {
        for (let header in headers) {
            request.setRequestHeader(header, headers[header]);
        }
    }

    // return the existing sheet else create it
    function getSheet(title, sheets) {
        return new Promise(function (resolve) {
            for (let i in sheets) {
                let sheet = sheets[i];

                if (sheet.properties.title === title) {
                    resolve(sheet);
                    return;
                }
            }

            spreadsheet
                .newSheet(title)
                .then(function (sheet) {
                    sheet = new Sheet(sheet);
                    sheets.push(sheet);
                    resolve(sheet);
                });
        });
    }

    function getPodcastsByGenre(params = [], callback, endCallback, startIndex = 2) {
        note
            .getPodcastsByGenre(params)
            .then(function (data) {
                let podcasts = [];

                for (let i in data.channels) {
                    let podcast = data.channels[i];
                    podcasts.push([
                        podcast.email ? podcast.email.trim() : 'N/A',
                        podcast.publisher.trim(),
                        podcast.title.trim(),
                        podcast.description.trim()
                    ]);
                }

                callback(podcasts, startIndex);

                if (data.has_next) {
                    // if not reached last page keep requesting for data
                    startIndex += data.channels.length;
                    params.page = data.next_page_number;
                    getPodcastsByGenre(params, callback, endCallback, startIndex);
                } else {
                    endCallback();
                }
            });
    }

    function init() {
        return new Promise(function (resolve) {
            let parse = G_SPREADSHEET_ID.split('::');
            let cachedDate = parse[1] || '';

            G_SPREADSHEET_ID = parse[0];

            if (G_SPREADSHEET_ID === '' || cachedDate !== getDateToday()) {
                spreadsheet
                    .create(getDateToday())
                    .then(function (response) {
                        G_SPREADSHEET_ID = response.spreadsheetId;
                        pushSpreadsheetId(G_SPREADSHEET_ID + '::' + getDateToday());
                        resolve();
                    });
            } else {
                resolve();
            }
        });
    }

    function getDateToday() {
        let today = new Date();

        today = [
            today.getFullYear(),
            String(today.getMonth() + 1).padStart(2, '0'),
            String(today.getDate()).padStart(2, '0')
        ];

        today = today.join('-');

        return today;
    }

    function showErrorMessage(categoryName) {
        processing = false;
        const errorMessage = document.getElementById('error-message');
        const capsule = document.querySelector('#podcast-request div.capsule');
        const failedCategory = document.querySelector('#error-message #failed-category-name');
        let miniLoad = document.querySelector('.mini-load');
        failedCategory.innerText = categoryName;
        errorMessage.style.display = 'block';
        capsule.style.display = 'flex';
        miniLoad.style.display = 'none';
        return null;
    }

    function hideErrorMessage() {
        const errorMessage = document.getElementById('error-message');
        errorMessage.style.display = 'none';
    }
}