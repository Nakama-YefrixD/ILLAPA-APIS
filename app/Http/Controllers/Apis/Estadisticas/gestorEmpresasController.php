<?php

namespace App\Http\Controllers\Apis\Estadisticas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\sectoristas;
use App\clientes;
use App\documentos;
use App\pagos;
use App\acciones;
use App\gestores;

use DB;

// VERSION 2.0


class gestorEmpresasController extends Controller
{
    public function mostrarClientes($gestorId)
    {
        $gestoresDatos = gestores::select( "u.email as userEmail", 
                                                "tdi.nombre as personaTipoIdentificacion",
                                                "p.numeroidentificacion as personaNumeroIdentificacion",
                                            "gestores.correo_id as gestorCorreo", 
                                            "p.nombre as personaNombre", 
                                            "p.imagen as personaImagen",
                                            "gestores.sector_id as sectorId")
                            ->join('users as u', 'u.id', '=', 'gestores.correo_id')
                            ->join('personas as p', 'p.id', '=', 'u.persona_id')
                            ->join('tiposDocumentosIdentidad as tdi', 'tdi.id', '=', 'p.tipoDocumentoIdentidad_id')
                            ->where('gestores.id', '=', $gestorId)
                            ->first();

        
        $clientesGestor = clientes::select('clientes.estado as clientesEstado', 'clientes.id as clienteId',
                                                "u.email as userEmail", 
                                                "tdi.nombre as personaTipoIdentificacion",
                                                "p.numeroidentificacion as personaNumeroIdentificacion",
                                                'p.nombre as personaNombre', "clientes.imagen as personaImagen")
                                    ->join('sectores as sct', 'sct.id', '=', 'clientes.sector_id')
                                    ->join('gestores as g', 'g.sector_id', '=', 'sct.id')
                                    ->join('users as u', 'u.id', '=', 'clientes.correo_id')
                                    ->join('personas as p', 'p.id', '=', 'u.persona_id')
                                    ->join('tiposDocumentosIdentidad as tdi', 'tdi.id', '=', 'p.tipoDocumentoIdentidad_id')
                                    ->where('g.id', '=', $gestorId)
                                    ->get();
        


        if (sizeof($clientesGestor) > 0){
            return json_encode(array("code" => true, "result"=>$clientesGestor, "gestor"=>$gestoresDatos , "load"=>true  ));
        }else{
            return json_encode(array("code" => false, "gestor"=>$gestoresDatos , "load"=>true));
        }
    }

    
    
}
