<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
    <link rel="stylesheet" href="{{ asset('mail/css/style.css')}}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
      <!-- jqueryConfirm -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
    <!-- toastr -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
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
          RECUPERAR <br>
        </div>
      </div>

      <div class="info-section">
        <h2>Recuperar contraseña
          <div class="border"></div>
        </h2>
          <p>
            Ingrese su nueva contraseña.
          </p>
          <form method="post" role="form" data-toggle="validator" id="frm_editarContrasena">
                @csrf
                <input type="password" class="form-control" name="contrasenaNueva" id="contrasenaNueva" >
                <input type="hidden" class="form-control" name="token" id="token" value="{{$token}}" >
                <div class="form-group boton"><br>
                    <button type="button" class="addexis form-control btn btn-block btn-success btn-lg" id="editarContasena">
                        Cambiar</button>
                </div>
            </form>
          
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
      <script src="{{ asset('plugins/jquery/jquery.min.js')}}"></script>
      <!-- JQUERY CONFIRM -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
      <!-- TOASTR -->
      <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
      <script type="text/javascript">
          $('#editarContasena').on('click', function(e) {
            let longitudContrasena = $('#contrasenaNueva').val().length; 
            console.log(longitudContrasena );
            if(longitudContrasena <= 8){

              toastr.error('La contraseña debe tener mas de 8 caracteres');
              
            }else{
              let data = $('#frm_editarContrasena').serialize();
                  console.log(data);
                  $.confirm({
                      icon: 'fa fa-question',
                      theme: 'modern',
                      animation: 'scale',
                      type: 'blue',
                      title: '¿Está seguro de cambiar su contraseña?',
                      content: false,
                      buttons: {
                          Confirmar: function () {
                              $.ajax({
                                  url: '/recuperar/contrasena',
                                  type: 'post',
                                  data: data ,
                                  dataType: 'json',
                                  success: function(response) {
                                      if(response['response'] == true) {
                                          toastr.success('Se cambio la contraseña correctamente');
                                          $('#contrasenaNueva').val(' ');

                                      } else {
                                          // toastr.error(response.responseText);
                                          toastr.error('Ocurrio un error al momento de cambiar la contraseña porfavor verifique si todos los campos estan correctos');
                                      }
                                  },
                                  error: function(response) {
                                      // toastr.error(response.responseText);
                                      toastr.error('Ocurrio un error al momento de cambiar la contraseña porfavor verifique si todos los campos estan correctos');
                                      
                                  }
                              });
                          },
                          Cancelar: function () {
                              
                          }
                      }
                  });
            }
          });
      </script>
  </body>
</html>
