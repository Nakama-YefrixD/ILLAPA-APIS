<?php

namespace App\Http\Controllers\Apis\Vencimientos;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\documentos;

class vencmientosFreeController extends Controller
{

    public function mostrarFechasSectoristasFree($sectoristaId)
    {
        $fechaActual = date('Y-m-d');
        $documentosEmpresa = documentos::select("documentos.fechavencimiento as documentosVencimiento")
                                        ->join('clientes as c', 'c.id', '=', 'documentos.cliente_id')
                                        ->join('sectores as sct', 'sct.id', '=', 'c.sector_id')
                                        ->join('sectoristas as scts', 'scts.id', '=', 'sct.sectorista_id')
                                        ->where('scts.id', '=', $sectoristaId)
                                        ->where('documentos.fechavencimiento', '<', $fechaActual)
                                        ->get();

        
        if (sizeof($documentosEmpresa) > 0){
            return json_encode(array("code" => true, "result"=>$documentosEmpresa, "load"=>true));
        }else{
            return json_encode(array("code" => false, "load"=>true));
        }

    }


    public function documentosSectoristasFree($sectoristaId)
    {//espera

        $documentosEmpresa = documentos::select("documentos.id as documentoId", "documentos.fechavencimiento as documentosVencimiento",
                                                    "p.nombre as personaNombre", "documentos.saldo as documentoSaldo",
                                                    "documentos.importe as documentoImporte","tm.nombre as documentoMoneda",
                                                     "c.id as clienteId")
                            ->join('tiposMonedas as tm','tm.id', '=', 'documentos.tipoMoneda_id' )
                            ->join('clientes as c', 'c.id', '=', 'documentos.cliente_id')
                            ->join('users as u', 'u.id', '=', 'c.correo_id')
                            ->join('personas as p', 'p.id', '=', 'u.persona_id')
                            ->join('sectores as sct', 'sct.id', '=', 'c.sector_id')
                            ->join('sectoristas as scts', 'scts.id', '=', 'sct.sectorista_id')
                            ->where('scts.id', '=', $sectoristaId)
                            ->get();

        
        if (sizeof($documentosEmpresa) > 0){
            return json_encode(array("code" => true, "result"=>$documentosEmpresa, "load"=>true));
        }else{
            return json_encode(array("code" => false, "load"=>true));
        }

    }

}
