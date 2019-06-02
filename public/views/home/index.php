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
</head>
<body>
    <section>
        <div>
            <form id="podcast-listing" class="form-group p-3" action="#">
                <div class="row">
                    <div class="col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">Genre</div>
                            </div>
                            <select id="genre-select" name="genreId" class="form-control">
                                <option value="null">All</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">Search</div>
                            </div>
                            <input name="search" type="search" class="search form-control" placeholder="keyword">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div>
            <table id="tbl-podcasts" class="display table table-bordered">
                <thead class="thead-dark text-center">
                <tr>
                    <th scope="col">Title</th>
                    <th scope="col">Host</th>
                    <th scope="col">Email</th>
                    <th scope="col">Description</th>
                </tr>
                </thead>
                <tbody class="bg-light"></tbody>
            </table>
        </div>
    </section>

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