<?php

namespace App\Http\Controllers\Apis\Estadisticas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\sectoristas;
use App\clientes;
use App\tramos;


class estadisticasFreeController extends Controller
{
    public function mostrarClientes($sectoristaId)
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
                            ->where('scts.id', '=', $sectoristaId)
                            ->get();
        


        if (sizeof($clientesSectorista) > 0){
            return json_encode(array("code" => true, "result"=>$clientesSectorista, "sectorista"=>$sectoristarDatos , "load"=>true  ));
        }else{
            return json_encode(array("code" => false, "sectorista"=>$sectoristarDatos , "load"=>true));
        }
    }

    public function estadisticasClientes($sectoristaId)
    {
        $fechaActual = date('Y-m-d');

        $tramosSocioIllapa = tramos::where('socio_id', '=', 1)
                                    ->get();

        //$documentosClientesSectoristaFree 
        $documentosClientesSectoristaFreesSectoristaFree = documentos::select('documentos.fechavencimiento as documentoFechaVencimieto',
                                                                'documentos.importe as documentoImporte', 
                                                                'documentos.saldo as documentoSaldo')
                                                        ->join('clientes as c', 'c.id', '=', 'documentos.cliente_id')
                                                        ->join('sectores as sct', 'sct.id', '=', 'c.sector_id')
                                                        ->where('sct.id', '=', $sectoristarDatos->sectorId)
                                                        ->get();

        
        
        $listaTramos = array(
                    array(
                        'desde' => 0,
                        'hasta' => 0,
                        'documentos' => 0,
                        'importe' => 0,
                        
                    ),
                    
                );

        $cont = 0;
        

        foreach($tramosSocioIllapa as $tramosSocioIllapas){
            $desde = $tramosSocioIllapas->tramoInicio;
            $hasta = $tramosSocioIllapas->tramoFin;
            $listaTramos[$cont]['desde'] = $desde;
            $listaTramos[$cont]['hasta'] = $hasta;
            
            $numeroDocumentos = 0;
            $importe = 0;
            foreach($documentosClientesSectoristaFree as $documentosClientesSectoristaFrees){

                if($documentosClientesSectoristaFrees->documentoSaldo <= 0){
                    
                }else{
    
                    $fechaVencimiento = $documentosClientesSectoristaFrees->documentoFechaVencimieto;
                    $dias	= (strtotime($fechaVencimiento)-strtotime($fechaActual))/86400;
                    if($desde <= $dias && $hasta >= $dias ){
                        $numeroDocumentos = $numeroDocumentos + 1;
                        $importe = $importe + $documentosClientesSectoristaFrees->documentoImporte;
                        $listaTramos[$cont]['documentos'] = $numeroDocumentos;
                        $listaTramos[$cont]['importe'] = $documentosClientesSectoristaFrees->documentoImporte+$importe;
                    }else{
                        $listaTramos[$cont]['documentos'] = $numeroDocumentos;
                        $listaTramos[$cont]['importe'] = $importe;
                        // $listaTramos[$cont]['importe'] = $interval->days;
                    }
    
    
                }


            }
            $cont = $cont+1;
        }
        
        $cantVencidos = 0;
        $cantVigentes = 0;
        $cantPagados = 0;

        $importePagados = 0;

        foreach($documentosClientesSectoristaFree as $documentosClientesSectoristaFreess){
            

            if($documentosClientesSectoristaFreess->documentoSaldo <= 0){
                $cantPagados = $cantPagados+1;
                $importePagados = $importePagados+$documentosClientesSectoristaFreess->documentoImporte;
            }else{

                $fechaVencimiento = $documentosClientesSectoristaFreess->documentoFechaVencimieto;
                $dias	= (strtotime($fechaVencimiento)-strtotime($fechaActual))/86400;
                if($dias <= 0){
                    $cantVencidos = $cantVencidos+1;
                }else{
                    $cantVigentes = $cantVigentes+1;
                }


            }
            
        }

        $countDocumentos = documentos::select(DB::raw('count(*) as cantDocumentos'))
                                        ->join('clientes as c', 'c.id', '=', 'documentos.cliente_id')
                                        ->join('sectores as sct', 'sct.id', '=', 'c.sector_id')
                                        ->where('sct.id', '=', $sectoristarDatos->sectorId)
                                        ->first();
        
        if($countDocumentos->cantDocumentos > 0){
            $porcentajeVencido = $cantVencidos*100;
            $porcentajeVencido = $porcentajeVencido/$countDocumentos->cantDocumentos;

            $porcentajeVigente = $cantVigentes*100;
            $porcentajeVigente = $porcentajeVigente/$countDocumentos->cantDocumentos;

            $porcentajePagados = $cantPagados*100;
            $porcentajePagados = $porcentajePagados/$countDocumentos->cantDocumentos;

            $porcentajeDocumentos = (object) [
                
                'vencido' => sprintf("%.2f", $porcentajeVencido),
                'vigente' => sprintf("%.2f", $porcentajeVigente),
                'pagado' => sprintf("%.2f", $porcentajePagados),
                'cantPagados' => $cantPagados,
                'importePagados' => sprintf("%.2f", $importePagados),

            ];
        }else{
            $porcentajeDocumentos = (object) [
                
                'vencido' => sprintf("%.2f", 0),
                'vigente' => sprintf("%.2f", 0),
                'pagado' => sprintf("%.2f", 0),
                'cantPagados' => 0,
                'importePagados' => sprintf("%.2f", 0),

            ];
        }
        
        

        
        if (sizeof($tramosSocioIllapa) > 0){
            return json_encode(array("code" => true, "cliente"=>$clienteSocio, "tramos"=>$listaTramos , "porcentaje"=>$porcentajeDocumentos ,"load"=>true ,  ));
        }else{
            return json_encode(array("code" => false, "cliente"=>$clienteSocio,  "porcentaje"=>$porcentajeDocumentos ,"load"=>true  ));
        }
       
    }
}
