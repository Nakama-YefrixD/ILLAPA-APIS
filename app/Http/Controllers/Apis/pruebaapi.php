<?php

namespace App\Http\Controllers\Apis;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\acciones;

class pruebaapi extends Controller
{
    public function MostrarAcciones()
    {
        $acs = acciones::get();

        return json_encode(
            array(
                "acciones" => $acs
            )
        );
    }
}
