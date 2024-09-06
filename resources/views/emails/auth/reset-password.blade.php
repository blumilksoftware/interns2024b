<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zresetuj hasło</title>
</head>

<header>
    <img src="favicon.png" alt="{{ config('app.name') }} Logo" width="150" height="50">
    <h1>{{ config('app.name') }}</h1>
</header>

<body>
<h1>Cześć {{ $user->name }},</h1>

<p>Otrzymaliśmy prośbę o zresetowanie hasła dla Twojego konta.</p>

<p>Aby zresetować swoje hasło, kliknij poniższy link:</p>

<a href="{{ $url }}">Zresetuj hasło</a>


<p>Pozdrawiamy,<br>{{ config('app.name') }}</p>
</body>
</html>
