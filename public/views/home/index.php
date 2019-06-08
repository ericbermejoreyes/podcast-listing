<?php
    global $config;
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Podcast Listing</title>
    <link rel="stylesheet" href="asset/css/shared/bootstrap.min.css">
    <link rel="stylesheet" href="asset/css/shared/main.css">
    <link rel="stylesheet" href="asset/css/shared/font-awesome.min.css">
</head>
<body>
    <div class="row position-absolute h-100 w-100">
        <div class="col-2 bg-dark p-5">
            <aside>
                <form id="podcast-listing" class="form-group" action="#">
                    <h3 class="text-white">Filter by:</h3>
                    <div class="input-group input-group-sm mt-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">Genre</div>
                        </div>
                        <select id="genre-select" name="genreId" class="form-control">
                            <option id="genre_all" value="null">All</option>
                        </select>
                    </div>
                    <div class="input-group input-group-sm mt-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">Search</div>
                        </div>
                        <input name="search" type="search" class="search form-control" placeholder="keyword">
                    </div>
                </form>
            </aside>
        </div>
        <div class="col-10 p-0">
            <section id="content" class="overflow-auto position-absolute w-100 h-100">
                <table id="tbl-podcasts" class="display table">
                    <thead class="text-center">
                    <tr>
                        <th scope="col">Title</th>
                        <th scope="col">Host</th>
                        <th scope="col">Email</th>
                        <th scope="col">Description</th>
                        <th scope="col">Note</th>
                        <th scope="col"></th>
                    </tr>
                    </thead>
                    <tbody class="bg-light"></tbody>
                </table>
            </section>
            <span class="processing" style="display: none;">
                <img src="./asset/img/animated/spinner.gif" class="spinner">
            </span>
        </div>
    </div>
    <script src="asset/js/shared/jquery.min.js"></script>
    <script src="asset/js/shared/bootstrap.min.js"></script>
    <script src="asset/js/shared/jquery.highlight.js"></script>
    <script>
        let lapiKey = "<?php echo $config['listennotes']['apikey']; ?>";
    </script>
    <script src="asset/js/shared/api.listennotes.js"></script>
    <script src="asset/js/home/app.js"></script>
</body>
</html>