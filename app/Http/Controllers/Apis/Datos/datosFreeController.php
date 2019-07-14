<?php

namespace App\Http\Controllers\Apis\Datos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\sectoristas;
use App\clientes;
use App\gestores;
use App\sectores;
use App\documentos;
use App\tiposDocumentosIdentidad;
use App\telefonos;
use App\direcciones;
use App\correos;



class datosFreeController extends Controller
{
    public function mostrarClientes($sectoristaId)
    {

        $tipos = tiposDocumentosIdentidad::select('id','nombre')
                                            ->where('estado', '=', 1)
                                            ->get();

        $fechaActual = date('Y-m-d');
        $sectoristarDatos = sectoristas::select( "sectoristas.correo_id as sectoristaCorreo",
                                            "sct.id as sectorId",
                                            "p.nombre as personaNombre", 
                                            "p.imagen as personaImagen")
                            ->join('sectores as sct', 'sct.sectorista_id', '=', 'sectoristas.id')
                            ->join('users as u', 'u.id', '=', 'sectoristas.correo_id')
                            ->join('personas as p', 'p.id', '=', 'u.persona_id')
                            ->where('sectoristas.id', '=', $sectoristaId)
                            ->first();

        $numeroDocumentosSectoristaFree = documentos::select("documentos.id")
                                                ->join('clientes as c', 'c.id', '=', 'documentos.cliente_id')
                                                ->join('sectores as sct', 'sct.id', '=', 'c.sector_id')
                                                ->where('sct.id', '=', $sectoristarDatos->sectorId)
                                                ->count();

        $sumaImportesDocumentosSectoristaFree = documentos::select("documentos.id", "documentos.importe as documentosImporte")
                                                        ->join('clientes as c', 'c.id', '=', 'documentos.cliente_id')
                                                        ->join('sectores as sct', 'sct.id', '=', 'c.sector_id')
                                                        ->where('sct.id', '=', $sectoristarDatos->sectorId)
                                                        ->sum("documentos.importe");
        
        $numeroDocumentosVencidosSectoristaFree = documentos::select("documentos.id")
                                                            ->join('clientes as c', 'c.id', '=', 'documentos.cliente_id')
                                                            ->join('sectores as sct', 'sct.id', '=', 'c.sector_id')
                                                            ->where('sct.id', '=', $sectoristarDatos->sectorId)
                                                            ->where('documentos.fechavencimiento', '<', $fechaActual)
                                                            ->count();

        $sumaImportesDocumentosVencidosSectoristaFree = documentos::select("documentos.id", "documentos.importe as documentosImporte")
                                                                    ->join('clientes as c', 'c.id', '=', 'documentos.cliente_id')
                                                                    ->join('sectores as sct', 'sct.id', '=', 'c.sector_id')
                                                                    ->where('sct.id', '=', $sectoristarDatos->sectorId)
                                                                    ->where('documentos.fechavencimiento', '<', $fechaActual)
                                                                    ->sum("documentos.importe");



        $clientesSectorista = clientes::select('clientes.estado as clientesEstado', 'clientes.id as clienteId',
                                                "u.email as userEmail", "u.id as userId", 
                                                "p.tipoDocumentoIdentidad_id as personaTipoIdentificacion",
                                                "tdi.nombre as tipoDocumentoIdentidad",
                                                "p.numeroidentificacion as personaNumeroIdentificacion",
                                                'scts.id as sectoristaId', 'p.nombre as personaNombre', 
                                                'p.imagen as personaImagen') 
                                            ->join('sectores as sct', 'sct.id', '=', 'clientes.sector_id')
                                            ->join('sectoristas as scts', 'scts.id', '=', 'sct.sectorista_id')
                                            ->join('users as u', 'u.id', '=', 'clientes.correo_id')
                                            ->join('personas as p', 'p.id', '=', 'u.persona_id')
                                            ->join('tiposDocumentosIdentidad as tdi', 'tdi.id', '=', 'p.tipoDocumentoIdentidad_id')
                                            ->where('scts.id', '=', $sectoristaId)
                                            ->get();
        


        if (sizeof($clientesSectorista) > 0){
            return json_encode(array("code" => true, 
                                    "result"=>$clientesSectorista, 
                                    "sectorista"=>$sectoristarDatos,
                                    "numeroDocumentos"=>$numeroDocumentosSectoristaFree,
                                    "sumaImporteDocumentos"=> sprintf("%.2f", $sumaImportesDocumentosSectoristaFree), 
                                    "numeroDocumentosVencidos"=>$numeroDocumentosVencidosSectoristaFree,
                                    "sumaImportesDocumentosVencidos"=> sprintf("%.2f", $sumaImportesDocumentosVencidosSectoristaFree), 
                                    "tipos" => $tipos,
                                    "load"=>true ));
        }else{
            return json_encode(array("code" => false, 
                                    "sectorista"=>$sectoristarDatos,
                                    "numeroDocumentos"=>$numeroDocumentosSectoristaFree,
                                    "sumaImporteDocumentos"=> sprintf("%.2f", $sumaImportesDocumentosSectoristaFree), 
                                    "numeroDocumentosVencidos"=>$numeroDocumentosVencidosSectoristaFree,
                                    "sumaImportesDocumentosVencidos"=> sprintf("%.2f", $sumaImportesDocumentosVencidosSectoristaFree), 
                                    "tipos" => $tipos,
                                    "load"=>true ));
        }
      
    }

    public function mostrarSectores($gestorfreeId)
    {
        $gestorFree = gestores::where('id','=', $gestorfreeId)
                                ->first();

        $sectores = sectores::where('id', '=', $gestorFree->sector_id)
                            ->get();

        return json_encode(array("code" => true, "sectores"=>$sectores, "load"=>true ));
    }

    public function clienteDatos($idCliente)
    {
        $telefonos = telefonos::where('cliente_id', '=', $idCliente)->get();
        $direcciones = direcciones::where('cliente_id', '=', $idCliente)->get();
        $correos = correos::where('cliente_id', '=', $idCliente)->get();


        if(sizeof($telefonos) > 0){
            $codeTelefonos = true;
        }else{
            $codeTelefonos = false;
        }
        
        if(sizeof($direcciones) > 0){
            $codeDirecciones = true;
        }else{
            $codeDirecciones = false;
        }

        if(sizeof($correos) > 0){
            $codeCorreos = true;
        }else{
            $codeCorreos = false;
        }

        return json_encode(array(
            "load"=>true,
            "codeTelefonos"=>$codeTelefonos,
            "telefonos" => $telefonos,
            "codeDirecciones"=>$codeDirecciones,
            "direcciones"=>$direcciones,
            "codeCorreos"=>$codeCorreos,
            "correos"=>$correos

        ));

    }

    public function agregarImagenCliente(Request $request)
    {
        $imagen = $request->imagen;
        $real = base64_decode($imagen);
        file_put_contents('xd.jpg', $real);
        


    }
}
