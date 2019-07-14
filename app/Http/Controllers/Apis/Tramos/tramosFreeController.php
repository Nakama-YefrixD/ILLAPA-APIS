<?php

namespace App\Http\Controllers\Apis\Tramos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class tramosFreeController extends Controller
{
    public function mostrarTramos($sectoristaId)
    {

        $socioEmpresa = socios::select("socios.id as sectoristaId", "socios.empresa_id as empresaId",
                                        "u.email as userEmail", "p.tipoidentificacion as personaTipoIdentificacion",
                                        "p.numeroidentificacion as personaNumeroIdentificacion",
                                        "socios.estado as socioEstado", "p.nombre as personaNombre", 
                                        "p.imagen as personaImagen")
                            ->join('users as u', 'u.id', '=', 'socios.correo_id')
                            ->join('personas as p', 'p.id', '=', 'u.persona_id')
                            ->where('socios.id', '=', $sectoristaId)
                            ->first();
        
        $tramosSocio = tramos::where('socio_id', '=', $sectoristaId)
                                    ->get();

                           

        if (sizeof($tramosSocio) > 0){
            return json_encode(array("code" => true, "result"=>$tramosSocio, "socio"=>$socioEmpresa , "load"=>true ));
        }else{
            return json_encode(array("code" => false, "message"=>"No hay empresas !", "socio"=>$socioEmpresa , "load"=>true));
        }

    }
}
