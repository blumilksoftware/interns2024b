<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ config('app.name') }}</title>
</head>

<body>

<header>
    <img src="" alt="{{ config('app.name') }} Logo" width="150" height="50">
    <h1>{{ config('app.name') }}</h1>
</header>

<body>
<p>Witaj,</p>
<a href="{{ $url }}">Link </a>
</body>
<footer>
</footer>
</body>

</html>
