<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class LoginController extends Controller
{
    public static function usuario()
    {
        $id = auth()->id();
        $usuario = User::select('p.imagen as personaImagen', 'p.nombre as personaNombre','e.nombre as empresaNombre')
                        ->join('personas as p','p.id','=', 'users.persona_id')
                        ->join('empresas as e', 'e.correo_id','=','users.id')
                        ->where('users.id', '=', $id)
                        ->first();



        return $usuario;
    }

}
