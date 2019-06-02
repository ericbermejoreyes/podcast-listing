$(() => {
    init();

    // initialize all necessary codes
    function init()
    {
        let page = 1;
        //load genres into select
        getGenreApi(genres => {
            for (let genre of genres) {
                let option = $('<option>');

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
            .on('scroll', function () {
                // check if scroll reached bottom
                if ($(window).scrollTop() + $(window).height() >= $(document).height()) {
                    page += 1;
                    populateTable(page);
                    console.log('loaded');
                }
            });
    }

    function populateTable(page) {
        let filters = {
            page: page || 1
        };
        let genre = JSON.parse($('form#podcast-listing select#genre-select').val());
        let search = $('form#podcast-listing input.search').val();

        if (genre) filters.genreId = genre.id;
        if (search) filters.search = search;

        getPodcastApi(filters, response => {
            addRows(response);
        });
    }

    function resetTable()
    {
        var table = $('table#tbl-podcasts tbody');
        table.empty();
    }

    function addRows(rows)
    {
        var table = $('table#tbl-podcasts');
        for (row of rows) {
            var tr = $('<tr>').attr({id: row.tokenId});
            tr
                .append($('<td>').text(row.title))
                .append($('<td>').text(row.host))
                .append($('<td>').text(row.email))
                .append($('<td>').text(row.description));

            table.append(tr);
        }
    }


    function updateGenresFromListennotes()
    {
        lapi.getGenres(data => {
            let genres = data.genres.map(genre => {
                return {
                    tokenId: genre.id,
                    name: genre.name
                }
            });

            updateGenresApi(genres);
        });
    }

    function updateGenresApi(genres) {
        $.ajax({
            url: './api/genres',
            method: 'put',
            data: {genres},
            headers: {
                'Content-Type': 'application/json'
            },
            success: response => {if (callback) callback(response);}
        });
    }

    function getGenreApi(callback)
    {
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

            updatePodcastApi(podcasts, () => {
                if (response.has_next) {
                    updatePodcastsFromListennotes(genre, page + 1);
                }
            });
        });
    }

    function updatePodcastApi(podcasts, callback)
    {
        $.ajax({
            url: './api/podcasts',
            method: 'put',
            data: {podcasts},
            headers: {
                'Content-Type': 'application/json'
            },
            success: response => {if (callback) callback(response); console.log(response)}
        });
    }

    function getPodcastApi(filters, callback)
    {
        $.ajax({
            url: './api/podcasts',
            data: filters,
            method: 'get',
            success: response => callback(response)
        })
    }
});