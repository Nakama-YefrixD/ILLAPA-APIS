<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\MensajeEnviado;
use App\User;
use Illuminate\Support\Str;
use App\personas;

class mensajesController extends Controller
{
    public function mensaje()
    {

        // MENSAJE DE PRUEBA
        $idUsuario = "gerson(1)";
        // Mail::to('gerson.vilca@tecsup.edu.pe')->send(new MensajeEnviado($idUsuario));
        $apitoken = "0wkzUtIedVXfjct0pTMLvxDs90XyQCy9XyBVichrYsKitWd1s1Sgtax6NgqU";
        $data = array(
            'token'     => $apitoken,
            "ruta"      => "confirmar",
            'titulo'    => "Confirmar Correo",
            "contenido" => "Hola ! Este correo fue enviado con el proposito de confirmar su registro en nuestra aplicacion móvil 'ILLAPA'",
        );
        Mail::to('gerson.vilca@tecsup.edu.pe')->send(new MensajeEnviado($data));
        return "Mensaje enviado ! ";

    }

    public function confirmar($token)
    {
        date_default_timezone_set('America/Lima');
        $fechaActual = date('Y-m-d H:i:s');
        
        $usuario = User::where('api_token', $token)->first();
        if($usuario){
            $usuario->email_verified_at = $fechaActual;
            $usuario->api_token         = Str::random(60);
            $usuario->update();
            $persona = personas::find($usuario->persona_id);
            $data = array(
                'nombre' => $persona->nombre
            );
            return view('emails.confirmarPagina')->with($data);
            
        }else{
            abort(403, 'Este usuario ya esta verificado.');
        }
    }
}