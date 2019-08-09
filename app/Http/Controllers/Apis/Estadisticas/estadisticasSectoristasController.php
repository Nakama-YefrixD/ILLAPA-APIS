<?php

namespace App\Http\Controllers\Apis\Estadisticas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\sectoristas;
use App\clientes;
use App\tramos;
use App\sectores;

class estadisticasSectoristasController extends Controller
{

    public function mostrarSectores($sectoristaId)
    {
        $sectoristarDatos = sectoristas::select( "u.email as userEmail", 
                                                "tdi.nombre as personaTipoIdentificacion",
                                                "p.numeroidentificacion as personaNumeroIdentificacion",
                                            "sectoristas.correo_id as sectoristaCorreo", 
                                            "p.nombre as personaNombre", 
                                            "p.imagen as personaImagen")
                            ->join('users as u', 'u.id', '=', 'sectoristas.correo_id')
                            ->join('personas as p', 'p.id', '=', 'u.persona_id')
                            ->join('tiposDocumentosIdentidad as tdi', 'tdi.id', '=', 'p.tipoDocumentoIdentidad_id')
                            ->where('sectoristas.id', '=', $sectoristaId)
                            ->first();

        $sectores = sectores::where('sectorista_id', '=', $sectoristaId)
                            ->get();

        if (sizeof($sectores) > 0){
            return json_encode(array(   "code" => true, 
                                        "result"=>$sectores,
                                        "sectorista"=>$sectoristarDatos,
                                        "load"=>true ));
        }else{
            return json_encode(array(   "code" => false,  
                                        "sectorista"=>$sectoristarDatos,
                                        "load"=>true));
        }
        
    }

    
    public function mostrarClientesTODO($sectorId)
    {
        $clientesSectorista = clientes::select('clientes.estado as clientesEstado', 'clientes.id as clienteId',
                                                "u.email as userEmail", 
                                                "tdi.nombre as personaTipoIdentificacion",
                                                "p.numeroidentificacion as personaNumeroIdentificacion",
                                                'scts.id as sectoristaId', 'p.nombre as personaNombre', "clientes.imagen as personaImagen")
                                        ->join('sectores as sct', 'sct.id', '=', 'clientes.sector_id')
                                        ->join('sectoristas as scts', 'scts.id', '=', 'sct.sectorista_id')
                                        ->join('users as u', 'u.id', '=', 'clientes.correo_id')
                                        ->join('personas as p', 'p.id', '=', 'u.persona_id')
                                        ->join('tiposDocumentosIdentidad as tdi', 'tdi.id', '=', 'p.tipoDocumentoIdentidad_id')
                                        ->where('sct.id', '=', $sectorId)
                                        ->get();
        
        if (sizeof($clientesSectorista) > 0){
            return json_encode(array("code" => true, 
                                    "result"=>$clientesSectorista, 
                                    "load"=>true ));
        }else{
            return json_encode(array("code" => false, 
                                    "load"=>true));
        }
    }
}
