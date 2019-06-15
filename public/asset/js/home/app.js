function initApp() {
    $(() => {
        let page = 1;
        let pageEnd = false;
        let pageLoad = false;
        let spreadsheet = new ApiSpreadsheet();
        let requestInterval = 1500;

        init();

        // initialize all necessary codes
        function init() {
            // update the whole database
            // updateGenresFromListennotes(() => {
            //     getGenreApi(genres => {
            //         for (let genre of genres) {
            //             updatePodcastsFromListennotes(genre);
            //         }
            //     });
            // });

            //load genres into select
            getGenreApi(genres => {
                for (let genre of genres) {
                    let option = $('<option>').attr({id: 'genre_' + genre.id});

                    option
                        .val(JSON.stringify({id: genre.id, tokenId: genre.tokenId}))
                        .text(genre.name);

                    $('select#genre-select').append(option);
                }

                populateTable();
            });

            // set events here
            $(document)
                .on('change', 'select#genre-select', function() {
                    if ($(this).val()) {
                        page = 1;
                        resetTable();
                        populateTable();
                    }
                })
                .on('input', 'form#podcast-listing input.search', function () {
                    resetTable();
                    populateTable();
                })
                .on('click', 'table#tbl-podcasts .star', toggleStarred)
                .on('dblclick', '.table#tbl-podcasts .note', toggleNote)
                .ajaxStart(function () {
                    let loading = $('#content + .processing');
                    if (loading.is(':visible') == false) {
                        loading.fadeIn('fast');
                    }
                })
                .ajaxComplete(function () {
                    let loading = $('#content + .processing');
                    if (loading.is(':visible') == true) {
                        loading.fadeOut('fast');
                    }
                })
                .on('click', '#spreadsheet-export', spreadsheetExportInit)
                .on('click', '#btn-export', spreadsheetExport);

            $('#content').scroll(function () {
                // check if scroll reached bottom
                if ($(this).scrollTop() >= $(this)[0].scrollHeight - $(this).height()) {
                    page += 1;
                    populateTable(page);
                }
            });
        }

        function toggleNote() {
            $(this).each((i, el) => {
                let note = $(this);
                let text = note.html().replace(/\<br\>/g, "\n");
                let editor = $('<textarea>').addClass('form-control note-editor').attr({rows: 3});
                let editFlag = false;

                if (!note.hasClass('note-edit')) {
                    let tokenId = $(this).closest('tr').attr('id');

                    note
                        .addClass('note-edit')
                        .html(editor.val(text));
                    editor.trigger('focus');

                    editor
                        .on('input', () => { editFlag = true; })
                        .on('blur', function () {
                            let text = $(this).val().replace(/\n/g, '<br>');
                            note
                                .html(text)
                                .removeClass('note-edit');
                            editor.remove();

                            if (editFlag) {
                                updateSinglePodcastApi(tokenId, {note: text});
                            }
                        });
                }
            });
        }

        function toggleStarred() {
            let starred = 0;
            let tokenId = $(this).closest('tr').attr('id');

            if ($(this).hasClass('fa-star')) {
                $(this).removeClass('fa-star').addClass('fa-star-o');
            } else {
                $(this).removeClass('fa-star-o').addClass('fa-star');
                starred = 1;
            }

            updateSinglePodcastApi(tokenId, {starred});
        }

        function populateTable(page) {
            let filters = {
                page: page || 1
            };

            let loading = $('<tr>')
                .addClass('text-center')
                .append(
                    $('<td>')
                        .attr({colspan: 4})
                        .append(
                            $('<img>')
                                .attr({src: './asset/img/animated/spinner.gif'})
                                .addClass('spinner')
                        )
                );

            let genre = JSON.parse($('form#podcast-listing select#genre-select').val());
            let search = $('form#podcast-listing input.search').val();

            if (genre) filters.genreId = genre.id;
            if (search) filters.search = search;

            $('table#tbl-podcasts tbody').append(loading);

            if (!pageLoad) {
                pageLoad = true;

                getPodcastApi(filters, response => {
                    if (response.length <= 0) {
                        pageEnd = true;
                    } else {
                        pageEnd = false;
                    }

                    addRows(response, () => {
                        $('table#tbl-podcasts tbody')
                            .unhighlight()
                            .highlight(search);

                        loading.remove();
                    });
                });
            }

            pageLoad = false;
        }

        function resetTable() {
            var table = $('table#tbl-podcasts tbody');
            table.empty();
        }

        function addRows(rows, callback) {
            var table = $('table#tbl-podcasts');
            for (row of rows) {
                var tr = $('<tr>').attr({id: row.tokenId});
                tr
                    .append($('<td>').html(row.title))
                    .append($('<td>').html(row.host))
                    .append($('<td>').html(row.email))
                    .append($('<td>').html(row.description))
                    .append($('<td>').addClass('note').html(row.note));

                if (row.starred == true) {
                    tr.append($('<td>').append($('<i>').addClass(['fa', 'fa-star', 'star'])));
                } else {
                    tr.append($('<td>').append($('<i>').addClass(['fa', 'fa-star-o', 'star'])));
                }

                table.append(tr);
            }

            if (callback) callback();
        }


        function updateGenresFromListennotes(callback) {
            lapi.getGenres(data => {
                let genres = data.genres.map(genre => {
                    return {
                        tokenId: genre.id,
                        name: genre.name
                    }
                });

                updateGenresApi(genres, callback);
            });
        }

        function updateGenresApi(genres, callback) {
            $.ajax({
                url: './api/genres',
                method: 'put',
                data: {genres},
                success: response => { if (callback) callback(response); }
            });
        }

        function getGenreApi(callback) {
            $.ajax({
                url: './api/genres',
                method: 'get',
                success: response => callback(response)
            });
        }

        function updatePodcastsFromListennotes(genre, page = 1) {
            lapi.getPodcastsByGenre({
                genre_id: genre.tokenId,
                region: 'us',
                page
            }, response => {
                podcasts = response.podcasts.map(podcast => {
                    return {
                        tokenId: podcast.id,
                        genreId: genre.id,
                        host: podcast.publisher,
                        title: podcast.title,
                        email: podcast.email,
                        description: podcast.description
                    }
                });

                updatePodcastsApi(podcasts, () => {
                    if (response.has_next) {
                        updatePodcastsFromListennotes(genre, page + 1);
                    }
                });
            });
        }

        function updateSinglePodcastApi(tokenId, podcast, callback) {
            $.ajax({
                url: './api/podcasts/' + tokenId.toString(),
                method: 'put',
                data: {podcast},
                headers: {
                    'Content-Type': 'application/json'
                },
                success: response => { if (callback) callback(response); }
            });
        }

        function updatePodcastsApi(podcasts, callback) {
            $.ajax({
                url: './api/podcasts',
                method: 'put',
                data: {podcasts},
                headers: {
                    'Content-Type': 'application/json'
                },
                success: response => { if (callback) callback(response); }
            });
        }

        function getPodcastApi(filters, callback) {
            $.ajax({
                url: './api/podcasts',
                data: filters,
                method: 'get',
                success: response => { if (callback) callback(response); }
            })
        }

        function toggleExportModal() {
            $('#export-modal').modal('toggle');
            $('#export-modal .modal-body input#spreadsheet-name').val('Podcast_Listing_' + Date.now());
        }

        function spreadsheetExportInit() {
            if (!spreadsheet.isSignedIn()) {
                spreadsheet
                    .signIn()
                    .then(() => {
                        toggleExportModal();
                    });
            }
        }

        // Begin export to google spreadsheet
        function spreadsheetExport() {
            let filename = $('#export-modal #spreadsheet-name').val();

            spreadsheet
                .create(filename)
                .then(response => {
                    let spreadsheetId = response.spreadsheetId;
                    let spreadsheetUrl = response.spreadsheetUrl;

                    getGenreApi(genres => {
                        createSheetPerGenre(spreadsheetId, genres);
                    });
                });
        }

        function createSheetPerGenre(spreadsheetId, genres)
        {
            if (genres.length > 0) {
                let genre = genres.shift();

                console.log(genres);

                setTimeout(() => {
                    spreadsheet
                        .newSheet(spreadsheetId, genre.name)
                        .then(sheetProperties => {
                            let sheet = new Sheet(spreadsheetId, sheetProperties);
                            populateSheet(sheet, genre.id, () => {
                                sheet.fixCellSize();
                                createSheetPerGenre(spreadsheetId, genres);
                            });
                        });
                }, requestInterval);
            }
        }

        // Populate each sheet with data
        function populateSheet(sheet, genreId, callback, page = 1, startIndex = 2)
        {
            setTimeout(() => {
                let filter = {
                    page: page,
                    genreId: genreId
                };

                getPodcastApi(filter, podcasts => {
                    if (podcasts.length > 0) {
                        podcasts = podcasts.map(podcast => {
                            return [podcast.title, podcast.host, podcast.email, podcast.description, podcast.note || ''];
                        });
                        sheet.update('A' + startIndex + ':E', podcasts);
                        populateSheet(sheet, genreId, callback, page + 1, startIndex + podcasts.length);
                    } else {
                        if (callback) callback(sheet);
                    }
                });
            }, requestInterval);
        }
    });
}