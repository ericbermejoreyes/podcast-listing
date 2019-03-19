<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
</head>
<body>
    <div id="loading">
        <img src="{{ asset('img/load.gif') }}" alt=""/>
    </div>
    <section id="content">
        @section('content')
            {{--contents goes in here--}}
        @show
    </section>
    <div id="footer">
        <a href="https://www.listennotes.com/" target="_blank">
            <img src="{{ asset('img/poweredby_listennotes.png') }}" alt="" class="powered-by">
        </a>
    </div>

    <script>
        // Client ID and API key from the Developer Console
        const G_CLIENT_ID = '{{ $_google["clientId"] }}';
        const G_API_KEY = '{{ $_google["apiKey"] }}';
        const L_API_KEY = '{{ $_listennotes['apiKey'] }}';

        let G_SPREADSHEET_ID = '{{ $_google['spreadsheetId'] }}';
    </script>
    <script src="{{ asset('js/spreadsheet.js') }}"></script>
    <script src="{{ asset('js/sheet.js') }}"></script>
    <script src="{{ asset('js/listennotes.js') }}"></script>
    @section('scripts')
    @show
    <script async defer src="https://apis.google.com/js/api.js"
            onload="handleClientLoad()"
            onreadystatechange="if (this.readyState === 'complete') this.onload()">
    </script>
</body>
</html>