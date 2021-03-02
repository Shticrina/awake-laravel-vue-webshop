<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Shopathon</title>

    <link href=" {{ mix('css/app.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">

    <script src="https://use.fontawesome.com/75ff9ef26d.js"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="//unpkg.com/json2csv@3.9.1/dist/json2csv.js"></script>
</head>

<body>
    <div id="app">
        <app></app>
    </div>

    <script src="{{ mix('js/bootstrap.js') }}"></script>
    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>