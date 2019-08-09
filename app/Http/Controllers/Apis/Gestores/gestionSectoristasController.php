<?php

namespace App\Http\Controllers\Apis\Gestores;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\sectoristas;
use App\documentos;
use App\sectores;
use App\clientes;
use App\acciones;
use DB;

class gestionSectoristasController extends Controller
{
    public function mostrarSectores($sectoristaId)
    {
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
                                                    ->where('documentos.saldo','>',0)
                                                    ->count();

        $sumaImportesDocumentosSectoristaFree = documentos::select("documentos.id", "documentos.importe as documentosImporte")
                                                        ->join('clientes as c', 'c.id', '=', 'documentos.cliente_id')
                                                        ->join('sectores as sct', 'sct.id', '=', 'c.sector_id')
                                                        ->where('sct.id', '=', $sectoristarDatos->sectorId)
                                                        ->where('documentos.saldo','>',0)
                                                        ->sum("documentos.importe");
        
        $numeroDocumentosVencidosSectoristaFree = documentos::select("documentos.id")
                                                            ->join('clientes as c', 'c.id', '=', 'documentos.cliente_id')
                                                            ->join('sectores as sct', 'sct.id', '=', 'c.sector_id')
                                                            ->where('sct.id', '=', $sectoristarDatos->sectorId)
                                                            ->where('documentos.fechavencimiento', '<', $fechaActual)
                                                            ->where('documentos.saldo','>',0)
                                                            ->count();

        $sumaImportesDocumentosVencidosSectoristaFree = documentos::select("documentos.id", "documentos.importe as documentosImporte")
                                                                    ->join('clientes as c', 'c.id', '=', 'documentos.cliente_id')
                                                                    ->join('sectores as sct', 'sct.id', '=', 'c.sector_id')
                                                                    ->where('sct.id', '=', $sectoristarDatos->sectorId)
                                                                    ->where('documentos.fechavencimiento', '<', $fechaActual)
                                                                    ->where('documentos.saldo','>',0)
                                                                    ->sum("documentos.importe");

        $sectores = sectores::where('sectorista_id', '=', $sectoristaId)
                            ->get();

        if (sizeof($sectores) > 0){
            return json_encode(array(   "code" => true, 
                                        "result"=>$sectores,
                                        "sectorista"=>$sectoristarDatos,
                                        "numeroDocumentos"=>$numeroDocumentosSectoristaFree,
                                        "sumaImporteDocumentos"=>sprintf("%.2f", $sumaImportesDocumentosSectoristaFree),
                                        "numeroDocumentosVencidos"=>$numeroDocumentosVencidosSectoristaFree,
                                        "sumaImportesDocumentosVencidos"=> sprintf("%.2f", $sumaImportesDocumentosVencidosSectoristaFree), 
                                        "load"=>true ));
        }else{
            return json_encode(array(   "code" => false,  
                                        "sectorista"=>$sectoristarDatos,
                                        "numeroDocumentos"=>$numeroDocumentosSectoristaFree,
                                        "sumaImporteDocumentos"=>sprintf("%.2f", $sumaImportesDocumentosSectoristaFree),
                                        "numeroDocumentosVencidos"=>$numeroDocumentosVencidosSectoristaFree,
                                        "sumaImportesDocumentosVencidos"=> sprintf("%.2f", $sumaImportesDocumentosVencidosSectoristaFree), 
                                        "load"=>true));
        }
        
    }

    public function mostrarClientesSector($sectorId)
    {
        $fechaActual = date('Y-m-d');
        $clientesSectorista = clientes::select('clientes.estado as clientesEstado', 'clientes.id as clienteId',
                                                    'scts.id as sectoristaId', 'p.nombre as personaNombre', 
                                                    "clientes.imagen as personaImagen", DB::raw('count(d.id) as numeroDocumentos'),
                                                    DB::raw("SUM(d.importe) as sumaImportesDocumentos") )
                                        ->leftjoin('sectores as sct', 'sct.id', '=', 'clientes.sector_id')
                                        ->leftjoin('sectoristas as scts', 'scts.id', '=', 'sct.sectorista_id')
                                        ->leftjoin('users as u', 'u.id', '=', 'clientes.correo_id')
                                        ->leftjoin('personas as p', 'p.id', '=', 'u.persona_id')
                                        ->leftjoin('documentos as d', 'd.cliente_id', '=', 'clientes.id')
                                        ->where('sct.id', '=', $sectorId)
                                        ->where('d.saldo','>',0)
                                        ->groupBy('clientes.id')
                                        ->get();

        if(sizeof($clientesSectorista) > 0){
            $listaClientesSectoristaFree = array(
                                                array(
                                                    'clienteId' => 0,
                                                    'sectoristaId' => 0,
                                                    'personaNombre' => 0,
                                                    'personaImagen' => 0,
                                                    'numeroDocumentos' => 0,
                                                    'sumaImportesDocumentos' => 0,
                                                    'numeroDocumentosVencidos' => 0,
                                                    'sumaImportesDocumentosVencidos' => 0,
                                                ),
                                            );
            $cont = 0;
            foreach($clientesSectorista as $clientesSectoristas){
                    
                $listaClientesSectoristaFree[$cont]['clienteId'] = $clientesSectoristas->clienteId;
                $listaClientesSectoristaFree[$cont]['sectoristaId'] = $clientesSectoristas->sectoristaId;
                $listaClientesSectoristaFree[$cont]['personaNombre'] = $clientesSectoristas->personaNombre;
                $listaClientesSectoristaFree[$cont]['personaImagen'] = $clientesSectoristas->personaImagen;
                $listaClientesSectoristaFree[$cont]['numeroDocumentos'] = $clientesSectoristas->numeroDocumentos;
                $listaClientesSectoristaFree[$cont]['sumaImportesDocumentos'] = sprintf("%.2f", $clientesSectoristas->sumaImportesDocumentos);

                $fechaProrroga = acciones::select('fechaprorroga as accionesFechaProrroga')
                                            ->where('cliente_id', '=', $clientesSectoristas->clienteId)
                                            ->latest()
                                            ->first();
                $prorroga = false;
                $fecha = $fechaActual;

                if($fechaProrroga){
                    $prorroga = true;
                    $fecha = $fechaProrroga->accionesFechaProrroga;
                }else{
                    $prorroga = false;
                }

                
                $clientesSectoristaEspecifico = clientes::select(DB::raw('count(d.id) as numeroDocumentosVencidos'),
                                                        DB::raw("SUM(d.importe) as sumaImportesDocumentosVencidos") )
                                                        ->leftjoin('sectores as sct', 'sct.id', '=', 'clientes.sector_id')
                                                        ->leftjoin('sectoristas as scts', 'scts.id', '=', 'sct.sectorista_id')
                                                        ->leftjoin('users as u', 'u.id', '=', 'clientes.correo_id')
                                                        ->leftjoin('personas as p', 'p.id', '=', 'u.persona_id')
                                                        ->leftjoin('documentos as d', 'd.cliente_id', '=', 'clientes.id')
                                                        ->where('clientes.id', '=', $clientesSectoristas->clienteId)
                                                        ->where('d.saldo', '>' , 0 )
                                                        ->where(function($query) use ($prorroga, $fecha, $fechaActual){
                                                            if($prorroga == false) {
                                                                $query->where('d.fechavencimiento', '<', $fechaActual );
                                                            }else{
                                                                if($fecha <= $fechaActual){
                                                                    $query->where('d.fechavencimiento', '<', $fechaActual );
                                                                }else{
                                                                    $query->where('d.fechavencimiento', '>', $fechaActual );
                                                                }
                                                            }
                                                        })
                                                        ->groupBy('clientes.id')
                                                        ->first();

                $numeroDocumentosVencidos = 0;
                
                if($clientesSectoristaEspecifico['numeroDocumentosVencidos'] != null){
                    $numeroDocumentosVencidos = $clientesSectoristaEspecifico['numeroDocumentosVencidos'];
                }
                $listaClientesSectoristaFree[$cont]['numeroDocumentosVencidos'] = $numeroDocumentosVencidos;
                


                $sumaImportesDocumentosVencidos = 0;
                if($clientesSectoristaEspecifico['sumaImportesDocumentosVencidos'] != null){
                    $sumaImportesDocumentosVencidos = $clientesSectoristaEspecifico['sumaImportesDocumentosVencidos'];
                }
                $listaClientesSectoristaFree[$cont]['sumaImportesDocumentosVencidos'] = sprintf("%.2f", $sumaImportesDocumentosVencidos);
                
                $cont = $cont+1;

            }
        }

        if (sizeof($clientesSectorista) > 0){
            return json_encode(array("code" => true, 
                                    "result"=>$listaClientesSectoristaFree, 
                                    "load"=>true ));
        }else{
            return json_encode(array("code" => false, 
                                    "load"=>true));
        }
        
    }

    // MOSTRAR TODOS LOS CLIENTES CON TODOS SUS DOCUMENTOS E IMPORTES POR VENCER Y VENCIDOS

    public function mostrarClientesTODO($sectorId)
    {
        $fechaActual = date('Y-m-d');
        $sectoristarDatos = sectoristas::select( "sectoristas.correo_id as sectoristaCorreo",
                                            "sct.id as sectorId",
                                            "p.nombre as personaNombre", 
                                            "p.imagen as personaImagen")
                            ->join('sectores as sct', 'sct.sectorista_id', '=', 'sectoristas.id')
                            ->join('users as u', 'u.id', '=', 'sectoristas.correo_id')
                            ->join('personas as p', 'p.id', '=', 'u.persona_id')
                            ->where('sct.id', '=', $sectorId)
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
                                                    'scts.id as sectoristaId', 'p.nombre as personaNombre', 
                                                    "clientes.imagen as personaImagen", DB::raw('count(d.id) as numeroDocumentos'),
                                                    DB::raw("SUM(d.importe) as sumaImportesDocumentos") )
                                        ->leftjoin('sectores as sct', 'sct.id', '=', 'clientes.sector_id')
                                        ->leftjoin('sectoristas as scts', 'scts.id', '=', 'sct.sectorista_id')
                                        ->leftjoin('users as u', 'u.id', '=', 'clientes.correo_id')
                                        ->leftjoin('personas as p', 'p.id', '=', 'u.persona_id')
                                        ->leftjoin('documentos as d', 'd.cliente_id', '=', 'clientes.id')
                                        ->where('sct.id', '=', $sectorId)
                                        ->groupBy('clientes.id')
                                        ->get();

        if(sizeof($clientesSectorista) > 0){
            $listaClientesSectoristaFree = array(
                                                array(
                                                    'clienteId' => 0,
                                                    'sectoristaId' => 0,
                                                    'personaNombre' => 0,
                                                    'personaImagen' => 0,
                                                    'numeroDocumentos' => 0,
                                                    'sumaImportesDocumentos' => 0,
                                                    'numeroDocumentosVencidos' => 0,
                                                    'sumaImportesDocumentosVencidos' => 0,
                                                ),
                                            );
            $cont = 0;
            foreach($clientesSectorista as $clientesSectoristas){
                $listaClientesSectoristaFree[$cont]['clienteId'] = $clientesSectoristas->clienteId;
                $listaClientesSectoristaFree[$cont]['sectoristaId'] = $clientesSectoristas->sectoristaId;
                $listaClientesSectoristaFree[$cont]['personaNombre'] = $clientesSectoristas->personaNombre;
                $listaClientesSectoristaFree[$cont]['personaImagen'] = $clientesSectoristas->personaImagen;
                $listaClientesSectoristaFree[$cont]['numeroDocumentos'] = $clientesSectoristas->numeroDocumentos;
                $listaClientesSectoristaFree[$cont]['sumaImportesDocumentos'] = sprintf("%.2f", $clientesSectoristas->sumaImportesDocumentos);


                $clientesSectoristaEspecifico = clientes::select(DB::raw('count(d.id) as numeroDocumentosVencidos'),
                                                        DB::raw("SUM(d.importe) as sumaImportesDocumentosVencidos") )
                                                        ->leftjoin('sectores as sct', 'sct.id', '=', 'clientes.sector_id')
                                                        ->leftjoin('sectoristas as scts', 'scts.id', '=', 'sct.sectorista_id')
                                                        ->leftjoin('users as u', 'u.id', '=', 'clientes.correo_id')
                                                        ->leftjoin('personas as p', 'p.id', '=', 'u.persona_id')
                                                        ->leftjoin('documentos as d', 'd.cliente_id', '=', 'clientes.id')
                                                        ->where('clientes.id', '=', $clientesSectoristas->clienteId)
                                                        ->where('d.fechavencimiento', '<', $fechaActual)
                                                        ->groupBy('clientes.id')
                                                        ->first();

                $numeroDocumentosVencidos = 0;
                
                if($clientesSectoristaEspecifico['numeroDocumentosVencidos'] != null){
                    $numeroDocumentosVencidos = $clientesSectoristaEspecifico['numeroDocumentosVencidos'];
                }
                $listaClientesSectoristaFree[$cont]['numeroDocumentosVencidos'] = $numeroDocumentosVencidos;
                


                $sumaImportesDocumentosVencidos = 0;
                if($clientesSectoristaEspecifico['sumaImportesDocumentosVencidos'] != null){
                    $sumaImportesDocumentosVencidos = $clientesSectoristaEspecifico['sumaImportesDocumentosVencidos'];
                }
                $listaClientesSectoristaFree[$cont]['sumaImportesDocumentosVencidos'] = sprintf("%.2f", $sumaImportesDocumentosVencidos);
                
                $cont = $cont+1;

            }

        }

        
        if (sizeof($clientesSectorista) > 0){
            return json_encode(array("code" => true, 
                                    "result"=>$listaClientesSectoristaFree, 
                                    "load"=>true ));
        }else{
            return json_encode(array("code" => false, 
                                    "load"=>true));
        }
    }
}
