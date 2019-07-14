<?php

namespace App\Http\Controllers\Apis;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\empresas;

class EmpresasController extends Controller
{
    public function mostrarEmpresas()
    {

        $empresas = empresas::where('estado', '=', 1)->get();

        if (sizeof($empresas) > 0){
            return json_encode(array("code" => 1, "result"=>$empresas));
        }else{
            return json_encode(array("code" => 0, "message"=>"No hay empresas !"));
        }
        
        


        // return $otro;
        // return json_encode(array("code" => 1, "result"=>$array));

    }
}
