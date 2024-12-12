@php
  $code = $code ?? 'Błąd';
  $message = $message ?? 'Błąd';
  $description = $description ?? 'Wystąpił nieoczekiwany błąd. Prosimy spróbować ponownie później.';
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $code }} - {{ $message }}</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap');

    * { margin: 0; padding: 0; }
    html, body { height: 100%; }
    body { display: flex; justify-content: center; align-items: center; font-family: 'Poppins', sans-serif; color: #333; position: relative;  max-width: 48rem; margin:auto }
    h1 { font-size: 3rem; margin-bottom: 1rem; color: #E4007D; }
    p { font-size: 1rem; margin-bottom: 2rem }
    b { font-size: 1.2rem; margin-bottom: 2rem; font-weight: bold }
    a { text-decoration: none; background-color: #E4007D; color: white; font-weight: 700; padding: .6rem 1rem; border-radius: .8rem; }
    .container { display: flex; flex-direction: column; align-items: center; text-align: center }
    .bg-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(255, 255, 255, 0.6); z-index: -10 }
    .blur-background { position: fixed; width: 58.33%; height: 100%; opacity: 0.7; filter: blur(48px); z-index: -10 }
    .gradient-bg {
      width: 100%;
      height: 100%;
      background: linear-gradient(to right, #ff80b5, #3b82f6);
      opacity: 0.3;
      clip-path: polygon(
        75% 42%, 98% 74%, 100% 35%, 93% 0%, 86% 0%, 75% 27%, 59% 55%, 50% 57%, 47% 44%, 
        48% 17%, 25% 54%, 0% 28%, 12% 75%, 25% 54%, 67% 100%, 75% 42%
      );
    }
  </style>
</head>
<body>
  <div class="bg-overlay"></div>
  <div class="blur-background" style="margin-top: 2rem;">
    <div class="gradient-bg"></div>
  </div>
  <div class="container">
    <b>{{ $code }}</b>
    <h1>{{ $message }}</h1>
    <p>{{ $description }}</p>
    @isset($link)
      <a href="{{ $link['url'] }}">{{ $link['text'] }}</a>
    @endisset
  </div>
</body>
</html>
