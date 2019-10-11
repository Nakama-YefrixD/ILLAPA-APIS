<?php

namespace App\Http\Controllers\Apis\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Mail\MensajeEnviado;
use App\User;
use Illuminate\Support\Str;
use App\personas;
use Illuminate\Support\Facades\Hash;

class recuperarContrasenaController extends Controller
{
    

    public function enviarMensajeRecuperacion($email)
    {

        // MENSAJE DE PRUEBA
        $usuario = User::where('email', $email)
                        ->first();

        $data = array(
            'token'     => $usuario->api_token,
            "ruta"      => "recuperar",
            'titulo'    => "RECUPERAR CONTRASEÑA",
            "contenido" => "Se acaba de solicitar una recuperacion de su contraseña por medio de la aplicación 'ILLAPA'",
            
        );
        

        Mail::to($email)->send(new MensajeEnviado($data));

        return json_encode(
            array(
                "code" => true,  
            )
        );
        

    }

    public function recuperarContrasena($token)
    {
        
        $usuario = User::where('api_token', $token)
                        ->first();
        $data = array(
            'token'     => $token,
            
        );
        if($usuario){
            return view('emails.recuperar')->with($data);
        }else{
            abort(403, 'Este usuario no ha pedido recuperar su contraseña.');
        }                        
        
    }

    public function cambiarContrasena(Request $request)
    {
        $token      = $request->token;
        $contrasena = $request->contrasena;



        $usuario = User::where('api_token', $token)
                        ->first();
        $usuario->password  = Hash::make($contrasena);
        $usuario->api_token =Str::random(60);

        if($usuario->update()){
            $output = array(
                'response'     =>  true,
            );
            
    
            echo json_encode($output);
        }else{
            $output = array(
                'response'     =>  false,
            );
            
    
            echo json_encode($output);
        }                        
        
    }
}
