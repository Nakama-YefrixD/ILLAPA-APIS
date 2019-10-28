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
use App\personas;
use App\User;
use App\tiposTelefonos;


use Peru\Jne\Dni;
use Peru\Sunat\Ruc;
use Peru\Http\ContextClient;

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
                                                'clientes.imagen as personaImagen') 
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



    public function buscarClientes(Request $request)
    {
        
        $dni = $request->dni;
        $idSectoristaSeleccionado = $request->idsectoristaSeleccionado;
        $tipoIdentificacion = $request->tipoIdentificacion;
        $nombre = $request->nombre;
        

        if($tipoIdentificacion == 1 ){
            
            $cs = new Dni(new ContextClient(), new DniParser());
            $person = $cs->get($dni);
            if ($person === false) {
                // echo $cs->getError();
                
                return json_encode(array("code" => false, "existente"=>false , "load"=>true ));
                exit();
                
            }
            $nombre = $person->nombres." ".$person->apellidoPaterno." ".$person->apellidoMaterno;

        }else if($tipoIdentificacion == 2){
            $cs = new Ruc();
            $cs->setClient(new ContextClient());

            $company = $cs->get($dni);
            if ($company === false) {
                // echo $cs->getError();
                
                return json_encode(array("code" => false, "existente"=>false , "load"=>true ));
                exit();
            }
            
            $nombre = $company->razonSocial;

        }

        

        $siPersona = personas::where('numeroidentificacion', '=', $dni)
                                ->first();

                                
        if($siPersona){
            
            $personaId = $siPersona->id;
            $nombre = $siPersona->nombre;
            $sectoristas = sectoristas::where('id', '=', $idSectoristaSeleccionado)
                                        ->get();
                                        
            $esMiCliente = 0;
            foreach($sectoristas as $sectoristasSocio){
                
                $sectores = sectores::where('sectorista_id', '=', $sectoristasSocio->id)
                                        ->get();

                foreach( $sectores as $sectoresSectorista ){
                    
                    $correoCliente = User::where('persona_id', '=', $personaId )
                                            ->get();
                    foreach($correoCliente as $correoClientes ){

                        $clientes = clientes::select("clientes.id", 
                                                        "clientes.sector_id",
                                                        "tdi.nombre as tipoDocumentoIdentidad")
                                            
                                            ->join('users as u', 'u.id', '=', 'clientes.correo_id')
                                            ->join('personas as p', 'p.id', '=', 'u.persona_id')
                                            ->join('tiposDocumentosIdentidad as tdi', 'tdi.id', '=', 'p.tipoDocumentoIdentidad_id')
                                            ->where('sector_id', '=', $sectoresSectorista->id)
                                            ->where('correo_id', '=', $correoClientes->id)
                                            ->first();

                        if($clientes){
                            return json_encode(array("code" => true, 
                                                        "existente"=>true  ,
                                                        "nombre" => $nombre ,
                                                        "load"=>true, 
                                                        "clienteId"=>$clientes->id, 
                                                        "sectorId"=>$clientes->sector_id,
                                                        "userId" => $correoClientes->id,
                                                        "image" => '$clientes->imagen',
                                                        "tipoDocumentoIdentidad" => $clientes->tipoDocumentoIdentidad,

                                                            ));
                        }
                    }
                }
            }
        }

        return json_encode(array("code" => false, "existente"=>true , "nombre"=>$nombre, "load"=>true ));
    }


    public function mostrarSectores($sectoristaId)
    {

        $sectores = sectores::where('sectorista_id', '=', $sectoristaId)
                            ->get();

        $tiposTelefonos = tiposTelefonos::select('id','nombre')
                                        ->where('estado','=',1)
                                        ->get();

        return json_encode(array("code" => true, 
                                    "sectores"=>$sectores, 
                                    "load"=>true,
                                    "tiposTelefonos"=>$tiposTelefonos));
    }

    public function clienteDatos($idCliente)
    {
        $telefonos = telefonos::select("telefonos.prefijo", "telefonos.numero", "tt.nombre as tipo", "telefonos.id as id")
                                ->where('cliente_id', '=', $idCliente)
                                ->join('tiposTelefonos as tt', 'tt.id','=','telefonos.tipotelefono_id')                        
                                ->get();

        $direcciones = direcciones::where('cliente_id', '=', $idCliente)->get();
        $correos = correos::where('cliente_id', '=', $idCliente)->get();

        $tiposTelefonos = tiposTelefonos::select('id','nombre')
                                        ->where('estado','=',1)
                                        ->get();


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
            "correos"=>$correos,
            "tiposTelefonos"=>$tiposTelefonos

        ));

    }

    public function agregarImagenCliente(Request $request)
    {
        $idCliente = $request->idCliente;
        $imagen = $request->image;
        $nombre = $request->nombre;
        $real = base64_decode($imagen);
        $ubicacion = "imagenes_clientes/".$nombre;
        file_put_contents($ubicacion,$real);

        $documento = clientes::find($idCliente);
        $documento->imagen = $ubicacion;
        $documento->update();


        
    }


    
}
