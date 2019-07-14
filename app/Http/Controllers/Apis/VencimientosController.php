<?php

namespace App\Http\Controllers\Apis;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\documentos;

class VencimientosController extends Controller
{
    public function mostrarFechasEmpresa($empresaId)
    {

        $documentosEmpresa = documentos::select("documentos.fechavencimiento as documentosVencimiento")
                            ->join('clientes as c', 'c.id', '=', 'documentos.cliente_id')
                            ->join('sectores as sct', 'sct.id', '=', 'c.sector_id')
                            ->join('socios as s', 's.id', '=', 'sct.socio_id')
                            ->join('empresas as e', 'e.id', '=', 's.empresa_id')
                            ->where('e.id', '=', $empresaId)
                            ->get();

        
        if (sizeof($documentosEmpresa) > 0){
            return json_encode(array("code" => true, "result"=>$documentosEmpresa, "load"=>true));
        }else{
            return json_encode(array("code" => false, "load"=>true));
        }

    }

    public function fechasDocumentosEmpresa($empresaId)
    {

        $documentosEmpresa = documentos::select("documentos.id as documentoId", "documentos.fechavencimiento as documentosVencimiento",
                                                    "p.nombre as personaNombre", "documentos.saldo as documentoSaldo",
                                                    "documentos.importe as documentoImporte", "documentos.moneda as documentoMoneda",
                                                    "c.id as clienteId")
                            ->join('clientes as c', 'c.id', '=', 'documentos.cliente_id')
                            ->join('users as u', 'u.id', '=', 'c.correo_id')
                            ->join('personas as p', 'p.id', '=', 'u.persona_id')
                            ->join('sectores as sct', 'sct.id', '=', 'c.sector_id')
                            ->join('socios as s', 's.id', '=', 'sct.socio_id')
                            ->join('empresas as e', 'e.id', '=', 's.empresa_id')
                            ->where('e.id', '=', $empresaId)
                            ->get();

        
        if (sizeof($documentosEmpresa) > 0){
            return json_encode(array("code" => true, "result"=>$documentosEmpresa, "load"=>true));
        }else{
            return json_encode(array("code" => false, "load"=>true));
        }

    }

}
