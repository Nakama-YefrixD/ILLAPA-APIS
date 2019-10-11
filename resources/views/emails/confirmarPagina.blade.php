<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
    <link rel="stylesheet" href="{{ asset('mail/css/style.css')}}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
  </head>
  <body>
    <div class="card middle">
      <div class="top-section">
        <img src="{{ asset('mail/img/negocios.jpg')}}" alt="">
        <div class="menuicon">
          <span class="s1"></span>
          <span class="s2"></span>
        </div>
        <div class="name">
          CONFIRMADO <br>
        </div>
      </div>

      <div class="info-section">
        <h2>Acerca de
          <div class="border"></div>
        </h2>
          <p>
            FELICIDADES {{ $nombre }} ! Acabas de confirmar tu correo electronico, ahora puedes empezar a gestionar tus cobranzas.
          </p>
        <h2>contactanos
          <div class="border"></div>
        </h2>
        <div class="s-m">
          <a href="" class="fab fa-facebook-f"></a>
          <a href="" class="fab fa-twitter"></a>
          <a href="" class="fab fa-instagram"></a>
          <a href="" class="fab fa-youtube"></a>
          <a href="" class="fab fa-whatsapp"></a>
        </div>
      </div>
    </div>
  </body>
</html>
