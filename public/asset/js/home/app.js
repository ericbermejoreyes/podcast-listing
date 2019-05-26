$(() => {
    let tablePodcasts = $('#tbl-podcasts');

    let dt = tablePodcasts.DataTable({
        fixHeader: true,
        ajax: {
            url: './api/podcasts',
            dataSrc: 'podcasts'
        },
        columns: [
            {data: 'title'},
            {data: 'host'},
            {data: 'email'},
            {data: 'description'}
        ]
    });

    dt.on('search.dt', () => {
        let keyword = dt.search();

        tablePodcasts.find('tbody').unhighlight().highlight(keyword);
    });

    function updateGenres()
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
            }
        });
    }

    function updatePodcasts(genre, page = 1) {
        lapi.getPodcastsByGenre({
            genre_id: genre.tokenId,
            region: 'us',
            page
        }, data => {

            podcasts = data.podcasts.map(podcast => {
                return {
                    tokenId: podcast.id,
                    genreId: genre.id,
                    host: podcast.publisher,
                    title: podcast.title,
                    email: podcast.email,
                    description: podcast.description
                }
            });

            updatePodcastApi(podcasts)

            if (data.has_next) {
                updatePodcasts(genre, page + 1);

            }
        });
    }

    function updatePodcastApi(podcasts)
    {
        $.ajax({
            url: './api/podcasts',
            method: 'put',
            data: {podcasts},
            headers: {
                'Content-Type': 'application/json'
            }
        })
    }
});