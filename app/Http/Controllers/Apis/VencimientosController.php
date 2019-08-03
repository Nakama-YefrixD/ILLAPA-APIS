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

    public function mostrarFechasSocio($socioId)
    {

        $documentosSocio = documentos::select("documentos.fechavencimiento as documentosVencimiento")
                            ->join('clientes as c', 'c.id', '=', 'documentos.cliente_id')
                            ->join('sectores as sct', 'sct.id', '=', 'c.sector_id')
                            ->join('socios as s', 's.id', '=', 'sct.socio_id')
                            ->join('empresas as e', 'e.id', '=', 's.empresa_id')
                            ->where('s.id', '=', $socioId)
                            ->get();

        
        if (sizeof($documentosSocio) > 0){
            return json_encode(array("code" => true, "result"=>$documentosSocio, "load"=>true));
        }else{
            return json_encode(array("code" => false, "load"=>true));
        }

    }

    public function mostrarFechasGestor($gestorId)
    {

        $documentosGestor = documentos::select("documentos.fechavencimiento as documentosVencimiento")
                            ->join('clientes as c', 'c.id', '=', 'documentos.cliente_id')
                            ->join('sectores as sct', 'sct.id', '=', 'c.sector_id')
                            ->join('gestores as g', 'g.sector_id', '=', 'sct.id')
                            ->where('g.id', '=', $gestorId)
                            ->get();

        
        if (sizeof($documentosGestor) > 0){
            return json_encode(array("code" => true, "result"=>$documentosGestor, "load"=>true));
        }else{
            return json_encode(array("code" => false, "load"=>true));
        }

    }


    public function fechasDocumentosEmpresa($empresaId)
    {

        $documentosEmpresa = documentos::select("documentos.id as documentoId", "documentos.fechavencimiento as documentosVencimiento",
                                                    "p.nombre as personaNombre", "documentos.saldo as documentoSaldo",
                                                    "documentos.importe as documentoImporte", 'tm.nombre as documentoMoneda',
                                                    "c.id as clienteId")
                            ->join('clientes as c', 'c.id', '=', 'documentos.cliente_id')
                            ->join('users as u', 'u.id', '=', 'c.correo_id')
                            ->join('personas as p', 'p.id', '=', 'u.persona_id')
                            ->join('sectores as sct', 'sct.id', '=', 'c.sector_id')
                            ->join('socios as s', 's.id', '=', 'sct.socio_id')
                            ->join('empresas as e', 'e.id', '=', 's.empresa_id')
                            ->join('tiposMonedas as tm','tm.id', '=', 'documentos.tipoMoneda_id' )
                            ->where('e.id', '=', $empresaId)
                            ->get();

        
        if (sizeof($documentosEmpresa) > 0){
            return json_encode(array("code" => true, "result"=>$documentosEmpresa, "load"=>true));
        }else{
            return json_encode(array("code" => false, "load"=>true));
        }

    }

    public function fechasDocumentosSocio($socioId)
    {
        $documentosSocios = documentos::select("documentos.id as documentoId", "documentos.fechavencimiento as documentosVencimiento",
                                                    "p.nombre as personaNombre", "documentos.saldo as documentoSaldo",
                                                    "documentos.importe as documentoImporte", 'tm.nombre as documentoMoneda',
                                                    "c.id as clienteId")
                                            ->join('clientes as c', 'c.id', '=', 'documentos.cliente_id')
                                            ->join('users as u', 'u.id', '=', 'c.correo_id')
                                            ->join('personas as p', 'p.id', '=', 'u.persona_id')
                                            ->join('sectores as sct', 'sct.id', '=', 'c.sector_id')
                                            ->join('socios as s', 's.id', '=', 'sct.socio_id')
                                            ->join('empresas as e', 'e.id', '=', 's.empresa_id')
                                            ->join('tiposMonedas as tm','tm.id', '=', 'documentos.tipoMoneda_id' )
                                            ->where('s.id', '=', $socioId)
                                            ->get();

        
        if (sizeof($documentosSocios) > 0){
            return json_encode(array("code" => true, "result"=>$documentosSocios, "load"=>true));
        }else{
            return json_encode(array("code" => false, "load"=>true));
        }

    }

    public function fechasDocumentosGestor($gestorId)
    {
        
        $documentosGestor = documentos::select("documentos.id as documentoId", "documentos.fechavencimiento as documentosVencimiento",
                                                    "p.nombre as personaNombre", "documentos.saldo as documentoSaldo",
                                                    "documentos.importe as documentoImporte", 'tm.nombre as documentoMoneda',
                                                    "c.id as clienteId")
                                            ->join('clientes as c', 'c.id', '=', 'documentos.cliente_id')
                                            ->join('users as u', 'u.id', '=', 'c.correo_id')
                                            ->join('personas as p', 'p.id', '=', 'u.persona_id')
                                            ->join('sectores as sct', 'sct.id', '=', 'c.sector_id')
                                            ->join('gestores as g', 'g.sector_id', '=', 'sct.id')
                                            ->join('tiposMonedas as tm','tm.id', '=', 'documentos.tipoMoneda_id' )
                                            ->where('g.id', '=', $gestorId)
                                            ->get();
        
        if (sizeof($documentosGestor) > 0){
            return json_encode(array("code" => true, "result"=>$documentosGestor, "load"=>true));
        }else{
            return json_encode(array("code" => false, "load"=>true));
        }

    }
    

    

}
