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
    <link rel="stylesheet" href="asset/css/shared/dataTables.bootstrap4.min.css">
    <style>
        .highlight {
            background-color: #ffdf7e;
        }
    </style>
</head>
<body>
    <section class="p-5">
        <table id="tbl-podcasts" class="display table table-bordered table-hover" style="width:100%">
            <thead class="bg-secondary text-white text-center">
            <tr>
                <th scope="col">Title</th>
                <th scope="col">Host</th>
                <th scope="col">Email</th>
                <th scope="col">Description</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </section>

    <script src="asset/js/shared/jquery.min.js"></script>
    <script src="asset/js/shared/bootstrap.min.js"></script>
    <script src="asset/js/shared/jquery.dataTables.min.js"></script>
    <script src="asset/js/shared/jquery.highlight.js"></script>
    <script src="asset/js/shared/dataTables.bootstrap4.min.js"></script>
    <script>
        let lapiKey = "<?php echo $config['listennotes']['apikey']; ?>";
    </script>
    <script src="asset/js/shared/api.listennotes.js"></script>
    <script src="asset/js/home/app.js"></script>
</body>
</html>