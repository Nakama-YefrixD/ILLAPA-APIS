<?php

namespace App\Http\Controllers\Apis;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\tiposDocumentosIdentidad;


class globales extends Controller
{
    public function tiposDocumentosIdentidad()
    {
        $tipos = tiposDocumentosIdentidad::select('id','nombre')
                                            ->where('estado', '=', 1)
                                            ->get();

        return json_encode(array("tipos" => $tipos, "load"=>true ));
    }
}
