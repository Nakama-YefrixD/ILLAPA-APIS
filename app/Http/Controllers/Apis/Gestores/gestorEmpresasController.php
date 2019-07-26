<?php

namespace App\Http\Controllers\Apis\Gestores;

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
        $fechaActual = date('Y-m-d');
        $gestorDatos = gestores::select( "gestores.correo_id as gestorCorreo",
                                            "gestores.sector_id as sectorId",
                                            "p.nombre as personaNombre", 
                                            "p.imagen as personaImagen")
                                        ->join('users as u', 'u.id', '=', 'gestores.correo_id')
                                        ->join('personas as p', 'p.id', '=', 'u.persona_id')
                                        ->where('gestores.estado' , '=', 1)
                                        ->where('gestores.id', '=', $gestorId)
                                        ->first();

        $numeroDocumentosSectoristaFree = documentos::select("documentos.id")
                                                    ->join('clientes as c', 'c.id', '=', 'documentos.cliente_id')
                                                    ->join('sectores as sct', 'sct.id', '=', 'c.sector_id')
                                                    ->where('sct.id', '=', $gestorDatos->sectorId)
                                                    ->where('documentos.saldo','>',0)
                                                    ->count();

        $sumaImportesDocumentosSectoristaFree = documentos::select("documentos.id", "documentos.importe as documentosImporte")
                                                        ->join('clientes as c', 'c.id', '=', 'documentos.cliente_id')
                                                        ->join('sectores as sct', 'sct.id', '=', 'c.sector_id')
                                                        ->where('sct.id', '=', $gestorDatos->sectorId)
                                                        ->where('documentos.saldo','>',0)
                                                        ->sum("documentos.importe");
        
        $numeroDocumentosVencidosSectoristaFree = documentos::select("documentos.id")
                                                            ->join('clientes as c', 'c.id', '=', 'documentos.cliente_id')
                                                            ->join('sectores as sct', 'sct.id', '=', 'c.sector_id')
                                                            ->where('sct.id', '=', $gestorDatos->sectorId)
                                                            ->where('documentos.fechavencimiento', '<', $fechaActual)
                                                            ->where('documentos.saldo','>',0)
                                                            ->count();

        $sumaImportesDocumentosVencidosSectoristaFree = documentos::select("documentos.id", "documentos.importe as documentosImporte")
                                                                    ->join('clientes as c', 'c.id', '=', 'documentos.cliente_id')
                                                                    ->join('sectores as sct', 'sct.id', '=', 'c.sector_id')
                                                                    ->where('sct.id', '=', $gestorDatos->sectorId)
                                                                    ->where('documentos.fechavencimiento', '<', $fechaActual)
                                                                    ->where('documentos.saldo','>',0)
                                                                    ->sum("documentos.importe");

        // echo $gestorDatos.'<br>';
        // echo $numeroDocumentosSectoristaFree.'<br>';
        // echo $sumaImportesDocumentosSectoristaFree.'<br>';
        // echo $numeroDocumentosVencidosSectoristaFree.'<br>';
        // echo $sumaImportesDocumentosVencidosSectoristaFree;
        $clientesGestor = gestores::select('gestores.id as gestorId','c.estado as clientesEstado', 'c.id as clienteId',
                                                    'p.nombre as personaNombre', 
                                                    "c.imagen as personaImagen", DB::raw('count(d.id) as numeroDocumentos'),
                                                    DB::raw("SUM(d.importe) as sumaImportesDocumentos") )
                                        ->leftjoin('sectores as sct', 'sct.id', '=', 'gestores.sector_id')
                                        ->leftjoin('clientes as c', 'c.sector_id', '=', 'sct.id')
                                        ->leftjoin('documentos as d', 'd.cliente_id', '=', 'c.id')
                                        ->leftjoin('users as u', 'u.id', '=', 'c.correo_id')
                                        ->leftjoin('personas as p', 'p.id', '=', 'u.persona_id')
                                        
                                        ->where('gestores.id', '=', $gestorId)
                                        ->where('d.saldo','>',0)
                                        ->groupBy('gestores.id')
                                        ->get();

        if(sizeof($clientesGestor) > 0){
            $listaClientesSectoristaFree = array(
                                                array(
                                                    'clienteId' => 0,
                                                    'gestorId' => 0,
                                                    'personaNombre' => 0,
                                                    'personaImagen' => 0,
                                                    'numeroDocumentos' => 0,
                                                    'sumaImportesDocumentos' => 0,
                                                    'numeroDocumentosVencidos' => 0,
                                                    'sumaImportesDocumentosVencidos' => 0,
                                                ),
                                            );
            $cont = 0;
            foreach($clientesGestor as $clientesSectoristas){
                
                $listaClientesSectoristaFree[$cont]['clienteId'] = $clientesSectoristas->clienteId;
                $listaClientesSectoristaFree[$cont]['gestorId'] = $clientesSectoristas->gestorId;
                $listaClientesSectoristaFree[$cont]['personaNombre'] = $clientesSectoristas->personaNombre;
                $listaClientesSectoristaFree[$cont]['personaImagen'] = $clientesSectoristas->personaImagen;
                $listaClientesSectoristaFree[$cont]['numeroDocumentos'] = $clientesSectoristas->numeroDocumentos;
                $listaClientesSectoristaFree[$cont]['sumaImportesDocumentos'] = sprintf("%.2f", $clientesSectoristas->sumaImportesDocumentos);

                $fechaProrroga = acciones::select('fechaprorroga as accionesFechaProrroga')
                                            ->where('cliente_id', '=', $clientesSectoristas->clienteId)
                                            ->latest()
                                            ->first();
                if($fechaProrroga){
                    if($fechaProrroga->accionesFechaProrroga == null){
                        $fecha = $fechaActual;
                        $signo = '<';
                    }else{
                        $fecha = $fechaProrroga->accionesFechaProrroga;
                        $signo = '>';
                    }
                    
                    
                }else{
                    $fecha = $fechaActual;
                    $signo = '<';
                }

                
                $clientesSectoristaEspecifico = clientes::select(DB::raw('count(d.id) as numeroDocumentosVencidos'),
                                                        DB::raw("SUM(d.importe) as sumaImportesDocumentosVencidos") )
                                                        ->leftjoin('sectores as sct', 'sct.id', '=', 'clientes.sector_id')
                                                        ->leftjoin('sectoristas as scts', 'scts.id', '=', 'sct.sectorista_id')
                                                        ->leftjoin('users as u', 'u.id', '=', 'clientes.correo_id')
                                                        ->leftjoin('personas as p', 'p.id', '=', 'u.persona_id')
                                                        ->leftjoin('documentos as d', 'd.cliente_id', '=', 'clientes.id')
                                                        ->where('clientes.id', '=', $clientesSectoristas->clienteId)
                                                        ->where('d.fechavencimiento', '<', $fechaActual )
                                                        ->where('d.fechavencimiento', $signo, $fecha )
                                                        ->where('d.saldo', '>' , 0 )
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
                                    "gestor"=>$gestorDatos,
                                    "numeroDocumentos"=>$numeroDocumentosSectoristaFree,
                                    "sumaImporteDocumentos"=>sprintf("%.2f", $sumaImportesDocumentosSectoristaFree),
                                    "numeroDocumentosVencidos"=>$numeroDocumentosVencidosSectoristaFree,
                                    "sumaImportesDocumentosVencidos"=> sprintf("%.2f", $sumaImportesDocumentosVencidosSectoristaFree), 
                                    "load"=>true ));
        }else{
            return json_encode(array("code" => false, 
                                    "gestor"=>$gestorDatos,
                                    "numeroDocumentos"=>$numeroDocumentosSectoristaFree,
                                    "sumaImporteDocumentos"=> sprintf("%.2f", $sumaImportesDocumentosSectoristaFree),  
                                    "numeroDocumentosVencidos"=>$numeroDocumentosVencidosSectoristaFree,
                                    "sumaImportesDocumentosVencidos"=> sprintf("%.2f", $sumaImportesDocumentosVencidosSectoristaFree), 
                                    "load"=>true));
        }
    }

    
    
    public function todosDocumentos($gestorId)
    {
        $documentosClientesSectoristaFree = documentos::select("documentos.*", 'td.nombre as tipoDocumentoIdentidad', 'tm.nombre as moneda')
                                                        ->join('tiposDocumentos as td','td.id', '=', 'documentos.tipoDocumento_id' )
                                                        ->join('tiposMonedas as tm','tm.id', '=', 'documentos.tipoMoneda_id' )
                                                        ->join('clientes as c', 'c.id', '=', 'documentos.cliente_id')
                                                        ->join('sectores as sct', 'sct.id', '=', 'c.sector_id')
                                                        ->join('gestores as g', 'sct.id', '=', 'g.sector_id')
                                                        ->where('g.id', '=', $gestorId)
                                                        ->get();
        
        
        $listaPagosClientesSectoristaFree = array(
                                                array(
                                                    'documentosId' => 0,
                                                    'pagosDocumentoId' => 0,
                                                    'pagosFechaEmision' => 0,
                                                    'pagosImporte' => 0,
                                                    'pagosTipo' => 0,
                                                    'pagosNumero' => 0,
                                                    
                                                ),
                                            );
        
        $cont = 0;
        foreach($documentosClientesSectoristaFree as $documentosClientesSectoristaFrees){
        
            $pagosDocumentoCliente = pagos::select("pagos.*", 'tp.nombre as tipoPago')
                                            ->where('documento_id', '=', $documentosClientesSectoristaFrees->id)
                                            ->join('tiposPagos as tp', 'tp.id', '=', 'pagos.tipoPago_id')    
                                            ->get();

            foreach($pagosDocumentoCliente as $pagosDocumentoClientes){
                $listaPagosClientesSectoristaFree[$cont]['documentosId'] = $pagosDocumentoClientes->documento_id;
                $listaPagosClientesSectoristaFree[$cont]['pagosDocumentoId'] = $pagosDocumentoClientes->documento_id;
                $listaPagosClientesSectoristaFree[$cont]['pagosFechaEmision'] = $pagosDocumentoClientes->fechaemision;
                $listaPagosClientesSectoristaFree[$cont]['pagosImporte'] = $pagosDocumentoClientes->importe;
                $listaPagosClientesSectoristaFree[$cont]['pagosTipo'] = $pagosDocumentoClientes->tipo;
                $listaPagosClientesSectoristaFree[$cont]['pagosNumero'] = $pagosDocumentoClientes->numero;
                $listaPagosClientesSectoristaFree[$cont]['tipoPago'] = $pagosDocumentoClientes->tipoPago;
                $cont = $cont+1;
            }
            

        }

        if (sizeof($documentosClientesSectoristaFree) > 0){
            return json_encode(array("code" => true, 
                                        "result"=>$documentosClientesSectoristaFree, 
                                        "pagos"=>$listaPagosClientesSectoristaFree,
                                        "load"=>true ));
        }else{
            return json_encode(array("code" => false,  "load"=>true));
        }

    }


    // MOSTRAR TODOS LOS CLIENTES CON TODOS SUS DOCUMENTOS E IMPORTES POR VENCER Y VENCIDOS

    public function mostrarClientesTODO($sectoristaId)
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
                                        ->where('scts.id', '=', $sectoristaId)
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
                                    "sectorista"=>$sectoristarDatos,
                                    "numeroDocumentos"=>$numeroDocumentosSectoristaFree,
                                    "sumaImporteDocumentos"=>sprintf("%.2f", $sumaImportesDocumentosSectoristaFree),
                                    "numeroDocumentosVencidos"=>$numeroDocumentosVencidosSectoristaFree,
                                    "sumaImportesDocumentosVencidos"=> sprintf("%.2f", $sumaImportesDocumentosVencidosSectoristaFree), 
                                    "load"=>true ));
        }else{
            return json_encode(array("code" => false, 
                                    "sectorista"=>$sectoristarDatos,
                                    "numeroDocumentos"=>$numeroDocumentosSectoristaFree,
                                    "sumaImporteDocumentos"=> sprintf("%.2f", $sumaImportesDocumentosSectoristaFree),  
                                    "numeroDocumentosVencidos"=>$numeroDocumentosVencidosSectoristaFree,
                                    "sumaImportesDocumentosVencidos"=> sprintf("%.2f", $sumaImportesDocumentosVencidosSectoristaFree), 
                                    "load"=>true));
        }
    }
    
}
