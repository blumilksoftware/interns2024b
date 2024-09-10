<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> {{ config('app.name') }}</title>
</head>

<header>
    <img src="" alt="{{ config('app.name') }} Logo" width="150" height="50">
    <h1>{{ config('app.name') }}</h1>
</header>

<body>
<h1>Cześć {{ $user->name }},</h1>

<p>Aby zweryfikować swoje konto, kliknij poniższy link:</p>

<a href="{{ $url }}">Zweryfikuj e-mail </a>

<p>Jeśli to nie Ty wysłałeś/aś tę prośbę, po prostu zignoruj tę wiadomość.</p>

<p>Pozdrawiamy,<br>{{ config('app.name') }}</p>
</body>

</html>
