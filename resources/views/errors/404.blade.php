<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>404 - Page Not Found</title>
</head>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap');

  * { margin: 0; padding: 0; }
  html, body { height: 100%; }
  body { display: flex; justify-content: center; align-items: center; font-family: 'Poppins', sans-serif; color: #333; position: relative }
  h1 { font-size: 6rem; margin-bottom: 1rem; color: #262c89 }
  p { font-size: 1.2rem; margin-bottom: 2rem }
  a { text-decoration: none; color: #262c89; font-size: 1rem }
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
<body>
  <div class="bg-overlay"></div>
  <div class="blur-background" style="margin-top: 2rem;">
    <div class="gradient-bg"></div>
  </div>
  <div class="container">
    <h1>404</h1>
    <p>Strona o podanym adresie nie istnieje.</p>
    <a href="/" class="">Wróć na stronę główną</a>
  </div>
</body>
</html>
