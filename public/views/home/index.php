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
        <div class="col-left bg-dark p-5">
            <aside>
                <form id="podcast-listing" class="form-group" action="#">
                    <h5 class="text-white">Filter by:</h5>
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
                    <h5 class="text-white mt-5">Export to:</h5>
                    <button type="button" id="spreadsheet-export" class="btn btn-primary btn-sm" data-target="#exportModal">Google Spreadsheet <i class="fa fa-file-excel-o"></i></button>
                </form>
            </aside>
        </div>
        <div class="col-right p-0">
            <section id="content" class="overflow-auto position-absolute h-100">
                <table id="tbl-podcasts" class="display table">
                    <thead class="text-center text-primary">
                    <tr>
                        <th scope="col">Show</th>
                        <th scope="col">Host</th>
                        <th scope="col">Email</th>
                        <th scope="col">Description</th>
                        <th scope="col">Note</th>
                        <th scope="col"></th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </section>
            <span class="processing" style="display: none;">
                <img src="./asset/img/animated/spinner.gif" class="spinner">
            </span>
        </div>
    </div>
    <div class="modal fade" id="export-modal" tabindex="-1" role="dialog" aria-labelledby="export-modal-title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <h5>Export to Google Spreadsheet:</h5>
                    <small class="text-muted mb-2" role="alert">
                        Note: every export will create a new spreadsheet document inside your google account / google drive
                    </small>
                    <div class="input-group input-group-sm mt-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">Filename</div>
                        </div>
                        <input type="text" id="spreadsheet-name" name="spreadsheet-name" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn-export" class="btn btn-primary" data-dismiss="modal">Export</button>
                </div>
            </div>
        </div>
    </div>
    <div id="toast-notifications">
        <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="mr-auto">Export Complete</strong>
                <small class="text-muted">just now</small>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body">
                See? Just like this.
            </div>
        </div>
    </div>
    <script src="asset/js/shared/jquery.min.js"></script>
    <script src="asset/js/shared/bootstrap.min.js"></script>
    <script src="asset/js/shared/jquery.highlight.js"></script>
    <script>
        let LAPIKEY = "<?php echo $config['listennotes']['apikey']; ?>";
        let G_API_KEY = "<?php echo $config['google']['apikey']; ?>";
        let G_CLIENT_ID = "<?php echo $config['google']['clientId']; ?>";
    </script>
    <script src="asset/js/shared/api.listennotes.js"></script>
    <script src="asset/js/shared/api.spreadsheet.js"></script>
    <script src="asset/js/shared/api.sheet.js"></script>
    <script src="asset/js/home/app.js"></script>
    <script async defer src="https://apis.google.com/js/api.js" onload="initApp()" onreadystatechange="if (this.readyState === 'complete') this.onload()"></script>
</body>
</html>