<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class LoginController extends Controller
{
    public static function usuario()
    {
        $id = auth()->id();
        $usuario = User::select('p.imagen as personaImagen', 'p.nombre as personaNombre')
                        ->join('personas as p','p.id','=', 'users.persona_id')
                        ->where('users.id', '=', $id)
                        ->first();



        return $usuario;
    }

}
