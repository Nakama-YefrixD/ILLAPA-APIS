<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
    <style type="text/css">
        body{
    margin: 0;
    padding: 0;
    background: #ffeaa7;
  }
  .card{
    width: 340px;
    background: #f1f1f1;
    overflow: hidden;
    font-family: "montserrat",sans-serif;
    box-shadow: 0 0 20px #00000070;
  }
  .middle{
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
  }
  .top-section{
    position: relative;
  }
  .top-section img{
    width: 100%;
  }
  .menuicon{
    position: absolute;
    width: 22px;
    left: 20px;
    top: 20px;
    cursor: pointer;
  }
  .menuicon span{
    width: 100%;
    height: 3px;
    background: #000;
    position: relative;
    display: block;
    margin-bottom: 6px;
    opacity: .5;
    transition: .4s;
  }
  .menuicon .s1{
    left: -5px;
  }
  .menuicon .s2{
    left: 5px;
  }
  .menuicon:hover span{
    left: 0;
  }
  .name{
    position: absolute;
    bottom: 20px;
    left: 20px;
    font-size: 40px;
    font-weight: 900;
    opacity: .5;
  }
  .name span{
    text-transform: uppercase;
    font-weight: 600;
  }
  
  .info-section{
    padding: 40px;
    padding-top: 0;
    color: #333;
  }
  h2{
    position: relative;
    font-size: 16px;
  }
  a{
    position: relative;
    font-size: 16px;
  }
  .border{
    width: 30px;
    height: 3px;
    background: #778beb;
    position: absolute;
    left: 0;
    bottom: -6px;
  }
  p{
    text-align: justify;
    font-size: 14px;
  }
  
  .s-m{
    text-align: center;
    margin-top: 20px;
  }
  .s-m a{
    text-decoration: none;
    font-size: 20px;
    color: #333;
    padding: 0 14px;
    transition: .4s;
  }
  .s-m a:hover{
    color: #778beb;
  }
  
    </style>
  </head>
  <body>
    <div class="card middle">
      <div class="info-section">
        <h2>{{ $data['titulo'] }}
          <div class="border"></div>
        </h2>
          <p>
            {{ $data['contenido'] }}
          </p>
        <h2>Click aquí: <a href="{{route ($data['ruta'], ['token' => $data['token'] ])}}">Link</a>
          <div class="border"></div>
        </h2>
        
      </div>
    </div>
  </body>
</html>
