<?php

namespace App\Http\Controllers\Apis\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\MensajeEnviado;

class correoConfirmacionController extends Controller
{
    public function reenviarCorreoConfirmacion($email)
    {
        $usuario = User::where('email', $email)
                        ->first();

        if($usuario->email_verified_at == null){
            $verificado = false;
            $apitoken = $usuario->api_token;
            $data = array(
                'token'     => $apitoken,
                "ruta"      => "confirmar",
                'titulo'    => "Confirmar Correo",
                "contenido" => "Hola ! Este correo fue enviado con el proposito de confirmar su registro en nuestra aplicacion móvil 'ILLAPA'",
            );
            Mail::to($email)->send(new MensajeEnviado($data));
            return json_encode($verificado);
        }else{
            $verificado = true;
            return json_encode($verificado);
        }

    }
}
