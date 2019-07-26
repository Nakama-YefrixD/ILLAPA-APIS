<?php

namespace App\Http\Controllers\Apis;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\empresas;
use App\socios;
use App\clientes;
use App\sectores;
use App\documentos;
use DB;

class EstadisticaController extends Controller
{
    public function mostrarEmpresas()
    {

        
        // select
        //         empresas.id,
        //         empresas.nombre,
        //         count(documentos.id) as c_documentos
        //     from empresas
        //     left join socios on socios.empresa_id = empresas.id
        //     left join sectores on sectores.socio_id = socios.id
        //     left join clientes on clientes.sector_id = sectores.id
        //     left join documentos on documentos.cliente_id = clientes.id
        //     group by empresas.id;
        $fechaActual = date('Y-m-d');
        $numeroDocumentosEmpresas = documentos::select("documentos.id")
                                                    ->join('clientes as c', 'c.id', '=', 'documentos.cliente_id')
                                                    ->join('sectores as sct', 'sct.id', '=', 'c.sector_id')
                                                    ->where('documentos.saldo','>',0)
                                                    ->count();
        $sumaImportesDocumentosEmpresas = documentos::select("documentos.id")
                                                    ->join('clientes as c', 'c.id', '=', 'documentos.cliente_id')
                                                    ->join('sectores as sct', 'sct.id', '=', 'c.sector_id')
                                                    ->where('documentos.saldo','>',0)
                                                    ->sum("documentos.importe");
        $numeroDocumentosVencidosEmpresas = documentos::select("documentos.id")
                                                        ->join('clientes as c', 'c.id', '=', 'documentos.cliente_id')
                                                        ->join('sectores as sct', 'sct.id', '=', 'c.sector_id')
                                                        ->where('documentos.fechavencimiento', '<', $fechaActual)
                                                        ->where('documentos.saldo','>',0)
                                                        ->count();
        $sumaImportesDocumentosVencidosEmpresas = documentos::select("documentos.id")
                                                            ->join('clientes as c', 'c.id', '=', 'documentos.cliente_id')
                                                            ->join('sectores as sct', 'sct.id', '=', 'c.sector_id')
                                                            ->where('documentos.fechavencimiento', '<', $fechaActual)
                                                            ->where('documentos.saldo','>',0)
                                                            ->sum("documentos.importe");

        $empresas = empresas::select("empresas.nombre as empresaNombre",
                                        "empresas.id as empresaId",
                                        "p.imagen as personaImagen",
                                        "p.numeroidentificacion as personaNumeroIdentificacion",
                                        "tdi.nombre as tipoDocumentoIdentidad",
                                        "u.email as userEmail",
                                        DB::raw('count(d.id) as countDocumentos'),
                                        DB::raw("SUM(d.importe) as sumaImportesDocumentos"))
                                        
                                    ->leftJoin('users as u', 'u.id', '=', 'empresas.correo_id')
                                    ->leftJoin('personas as p', 'p.id', '=', 'u.persona_id')
                                    ->leftJoin('socios as s', 's.empresa_id', '=', 'empresas.id')
                                    ->leftJoin('sectores as sct', 'sct.socio_id', '=', 's.id')
                                    ->leftJoin('clientes as c', 'c.sector_id', '=', 'sct.id')
                                    ->leftJoin('documentos as d', 'd.cliente_id', '=', 'c.id')
                                    ->leftjoin('tiposDocumentosIdentidad as tdi', 'tdi.id', '=', 'p.tipoDocumentoIdentidad_id')
                                    ->where('empresas.estado', '=', 1)
                                    // ->where('d.saldo','>',0)
                                    ->groupBy('empresas.id')
                                    ->get();


        if(sizeof($empresas) > 0){
            $listaEmpresas = array(
                array(
                    'empresaId' => 0,
                    'empresaNombre' => 0,
                    'personaImagen' => 0,
                    'tipoDocumentoIdentidad' => 0,
                    'numeroDocumentoIdentidad' => 0,
                    'userEmail' => 0,
                    'numeroDocumentos' => 0,
                    'sumaImportesDocumentos' => 0,
                    'numeroDocumentosVencidos' => 0,
                    'sumaImportesDocumentosVencidos' => 0,
                ),
            );

            $cont = 0;
            foreach($empresas as $empresa){

                $listaEmpresas[$cont]['empresaId'] = $empresa->empresaId;
                $listaEmpresas[$cont]['empresaNombre'] = $empresa->empresaNombre;
                $listaEmpresas[$cont]['personaImagen'] = $empresa->personaImagen;
                $listaEmpresas[$cont]['tipoDocumentoIdentidad'] = $empresa->tipoDocumentoIdentidad;
                $listaEmpresas[$cont]['numeroDocumentoIdentidad'] = $empresa->personaNumeroIdentificacion;
                $listaEmpresas[$cont]['userEmail'] = $empresa->userEmail;
                $listaEmpresas[$cont]['numeroDocumentos'] = $empresa->countDocumentos;
                $listaEmpresas[$cont]['sumaImportesDocumentos'] = sprintf("%.2f", $empresa->sumaImportesDocumentos); 


                $empresasDocumentosVencidas = empresas::select( DB::raw('count(d.id) as numeroDocumentosVencidos'),
                                                                DB::raw("SUM(d.importe) as sumaImportesDocumentosVencidos") )
                                                        ->leftJoin('users as u', 'u.id', '=', 'empresas.correo_id')
                                                        ->leftJoin('personas as p', 'p.id', '=', 'u.persona_id')
                                                        ->leftJoin('socios as s', 's.empresa_id', '=', 'empresas.id')
                                                        ->leftJoin('sectores as sct', 'sct.socio_id', '=', 's.id')
                                                        ->leftJoin('clientes as c', 'c.sector_id', '=', 'sct.id')
                                                        ->leftJoin('documentos as d', 'd.cliente_id', '=', 'c.id')
                                                        ->where('empresas.id', '=', $empresa->empresaId)
                                                        ->where('d.fechavencimiento', '<', $fechaActual)
                                                        ->groupBy('empresas.id')
                                                        ->first();
                $numeroDocumentosVencidos = 0;
                if($empresasDocumentosVencidas['numeroDocumentosVencidos'] != null){
                    $numeroDocumentosVencidos = $empresasDocumentosVencidas['numeroDocumentosVencidos'];
                }
                $listaEmpresas[$cont]['numeroDocumentosVencidos'] = $numeroDocumentosVencidos;

                $sumaImportesDocumentosVencidos = 0;
                if($empresasDocumentosVencidas['sumaImportesDocumentosVencidos'] != null){
                    $sumaImportesDocumentosVencidos = $empresasDocumentosVencidas['sumaImportesDocumentosVencidos'];
                }
                $listaEmpresas[$cont]['sumaImportesDocumentosVencidos'] = sprintf("%.2f", $sumaImportesDocumentosVencidos);
                $cont = $cont+1;
            }
        }
        // $documentos = documentos::where('fechavencimiento', '>', $fechaActual)->count();
                        
        if (sizeof($empresas) > 0){
            return json_encode(array("code" => true, 
                                    "result"=>$listaEmpresas, 
                                    "numeroDocumentos" => $numeroDocumentosEmpresas,
                                    "sumaImportesDocumentos"=>  sprintf("%.2f", $sumaImportesDocumentosEmpresas),
                                    "numeroDocumentosVencidos" => $numeroDocumentosVencidosEmpresas,
                                    "sumaImportesDocumentosVencidos"=> sprintf("%.2f", $sumaImportesDocumentosVencidosEmpresas),
                                    "load"=> true ));
        }else{
            return json_encode(array("code" => false,  
                                    "numeroDocumentos"=> $numeroDocumentosEmpresas,
                                    "sumaImportesDocumentos"=> sprintf("%.2f", $sumaImportesDocumentosEmpresas),  
                                    "numeroDocumentosVencidos"=> $numeroDocumentosVencidosEmpresas,
                                    "sumaImportesDocumentosVencidos" => sprintf("%.2f", $sumaImportesDocumentosVencidosEmpresas), 
                                    "load"=>true ));
        }

    }

    public function mostrarSocios($empresaid)
    {

        $empresa = empresas::select("empresas.nombre as empresaNombre", "u.email as userEmail", 
                                        "tdi.nombre as personaTipoIdentificacion", 
                                        "p.numeroidentificacion as personaNumeroIdentificacion",
                                        "p.imagen as personaImagen")
                            ->join('users as u', 'u.id', '=', 'empresas.correo_id')
                            ->join('personas as p', 'p.id', '=', 'u.persona_id')
                            ->join('tiposDocumentosIdentidad as tdi', 'tdi.id', '=', 'p.tipoDocumentoIdentidad_id')
                            ->where('empresas.id', '=', $empresaid)
                            ->first();

        // $sociosEmpresa = socios::where('empresa_id', '=', $empresaid)->get();
        $sociosEmpresa = socios::select("socios.id as socioId", "socios.empresa_id as empresaId", 
                                        "u.email as userEmail", "tdi.nombre as personaTipoIdentificacion", 
                                        "p.numeroidentificacion as personaNumeroIdentificacion",
                                        "socios.estado as socioEstado", "p.nombre as personaNombre", 
                                        "p.imagen as personaImagen")
                            ->join('users as u', 'u.id', '=', 'socios.correo_id')
                            ->join('personas as p', 'p.id', '=', 'u.persona_id')
                            ->join('tiposDocumentosIdentidad as tdi', 'tdi.id', '=', 'p.tipoDocumentoIdentidad_id')
                            ->where('socios.empresa_id', '=', $empresaid)
                            ->get();

                            // select s.id, s.empresa_id, s.estado, p.nombre, p.imagen
                            // from socios s, personas p, users u
                            // where s.correo_id = u.id && u.persona_id = p.id && empresa_id = ;

        if (sizeof($sociosEmpresa) > 0){
            return json_encode(array("code" => true, "result"=>$sociosEmpresa, "empresa"=>$empresa,"load"=>true  ));
        }else{
            return json_encode(array("code" => false, "message"=>"No hay empresas !", "empresa"=>$empresa, "load"=>true));
        }

    }

    public function mostrarClientes($socioId)
    {

        $socioEmpresa = socios::select("socios.id as socioId", "socios.empresa_id as empresaId",
                                        "u.email as userEmail", "tdi.nombre as personaTipoIdentificacion",
                                        "p.numeroidentificacion as personaNumeroIdentificacion",
                                        "socios.estado as socioEstado", "p.nombre as personaNombre", 
                                        "p.imagen as personaImagen")
                            ->join('users as u', 'u.id', '=', 'socios.correo_id')
                            ->join('personas as p', 'p.id', '=', 'u.persona_id')
                            ->join('tiposDocumentosIdentidad as tdi', 'tdi.id', '=', 'p.tipoDocumentoIdentidad_id')
                            ->where('socios.id', '=', $socioId)
                            ->first();
        
        // $sociosEmpresa = socios::where('empresa_id', '=', $empresaid)->get();
        $clientesSocio = clientes::select('clientes.estado as clientesEstado', 's.id as socioId', 
                                            'clientes.id as clienteId',
                                            "u.email as userEmail", "tdi.nombre as personaTipoIdentificacion",
                                            "p.numeroidentificacion as personaNumeroIdentificacion",
                                            "clientes.imagen as personaImagen",
                                            'p.nombre as personaNombre')
                            ->join('sectores as sct', 'sct.id', '=', 'clientes.sector_id')
                            ->join('socios as s', 's.id', '=', 'sct.socio_id')
                            ->join('users as u', 'u.id', '=', 'clientes.correo_id')
                            ->join('personas as p', 'p.id', '=', 'u.persona_id')
                            ->join('tiposDocumentosIdentidad as tdi', 'tdi.id', '=', 'p.tipoDocumentoIdentidad_id')
                            ->where('s.id', '=', $socioId)
                            ->get();

                            
        // select c.estado, s.id, p.nombre
        // from clientes c, sectores sct, sectoristas scts, socios s, users u, personas p
        // where c.sector_id = sct.id && sct.sectorista_id = scts.id && scts.socio_id = s.id && u.id = c.correo_id && u.persona_id = p.id ;

        if (sizeof($clientesSocio) > 0){
            return json_encode(array("code" => true, "result"=>$clientesSocio, "socio"=>$socioEmpresa, "load"=>true  ));
        }else{
            return json_encode(array("code" => false, "message"=>"No hay empresas !", "socio"=>$socioEmpresa, "load"=>true));
        }

    }

    public function mostrarCliente($clienteid)
    {

      
        $fechaActual = date('Y-m-d');
        // $sociosEmpresa = socios::where('empresa_id', '=', $empresaid)->get();
        $clienteSocio = clientes::select('clientes.estado as clientesEstado', 's.id as socioId', 
                                            'clientes.sector_id as sector_id',
                                            'clientes.id as clienteId', 'clientes.imagen as personaImagen',
                                            "u.email as userEmail", "tdi.nombre as personaTipoIdentificacion",
                                            "p.numeroidentificacion as personaNumeroIdentificacion",
                                            'p.nombre as personaNombre')
                            ->join('sectores as sct', 'sct.id', '=', 'clientes.sector_id')
                            ->join('socios as s', 's.id', '=', 'sct.socio_id')
                            ->join('users as u', 'u.id', '=', 'clientes.correo_id')
                            ->join('personas as p', 'p.id', '=', 'u.persona_id')
                            ->join('tiposDocumentosIdentidad as tdi', 'tdi.id', '=', 'p.tipoDocumentoIdentidad_id')
                            ->where('clientes.id', '=', $clienteid)
                            ->first();

        // select c.estado, s.id, p.nombre
        // from clientes c, sectores sct, sectoristas scts, socios s, users u, personas p
        // where c.sector_id = sct.id && sct.sectorista_id = scts.id && scts.socio_id = s.id && u.id = c.correo_id && u.persona_id = p.id ;

        $tramosSocio = sectores::select('t.nombre as tramoNombre', 't.inicio as tramoInicio',
                                            't.fin as tramoFin', 't.estado as tramoEstado')
                            ->join('tramos as t', 't.socio_id', '=', 'sectores.socio_id')
                            ->where('sectores.id', '=', $clienteSocio->sector_id)
                            ->orderBy('t.inicio')
                            ->get();

        $documentosCliente = documentos::select('documentos.fechavencimiento as documentoFechaVencimieto',
                                                'documentos.importe as documentoImporte', 
                                                'documentos.saldo as documentoSaldo' )
                                        ->where('documentos.cliente_id', '=', $clienteid)
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
        

        foreach($tramosSocio as $tramosSocios){
            $desde = $tramosSocios->tramoInicio;
            $hasta = $tramosSocios->tramoFin;
            $listaTramos[$cont]['desde'] = $desde;
            $listaTramos[$cont]['hasta'] = $hasta;
            
            $numeroDocumentos = 0;
            $importe = 0;
            foreach($documentosCliente as $documentosClientes){
                
                if($documentosClientes->documentoSaldo <= 0){
                    
                }else{
                    $fechaVencimiento = $documentosClientes->documentoFechaVencimieto;
                    $dias	= (strtotime($fechaActual)-strtotime($fechaVencimiento))/86400;
                    if($desde <= $dias && $hasta >= $dias ){
                        $numeroDocumentos = $numeroDocumentos + 1;
                        $importe = $importe + $documentosClientes->documentoImporte;
                        $listaTramos[$cont]['documentos'] = $numeroDocumentos;
                        $listaTramos[$cont]['importe'] = $importe;
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

        foreach($documentosCliente as $documentosClientess){
            if($documentosClientess->documentoSaldo <= 0){
                $cantPagados = $cantPagados+1;
                $importePagados = $importePagados+$documentosClientess->documentoImporte;
            }else{
                $fechaVencimiento = $documentosClientess->documentoFechaVencimieto;
                $dias	= (strtotime($fechaActual)-strtotime($fechaVencimiento))/86400;
                if($dias <= 0){
                    $cantVencidos = $cantVencidos+1;
                }else{
                    $cantVigentes = $cantVigentes+1;
                }
            }
        }


        $countDocumentos = documentos::select(DB::raw('count(*) as cantDocumentos'))
                                        ->where('cliente_id', '=', $clienteid)
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
                'cantPagados' => sprintf("%.2f", $cantPagados),
                'cantVigentes' => sprintf("%.2f", $cantVigentes), 
                'cantVencidos' => sprintf("%.2f", $cantVencidos), 
                'cantTotal' => sprintf("%.2f", $countDocumentos->cantDocumentos), 
                'importePagados' => sprintf("%.2f", $importePagados),

            ];
        }else{
            $porcentajeDocumentos = (object) [
                
                'vencido' => sprintf("%.2f", 0),
                'vigente' => sprintf("%.2f", 0),
                'pagado' => sprintf("%.2f", 0),
                'cantPagados' => sprintf("%.2f", $cantPagados),
                'cantVigentes' => sprintf("%.2f", $cantVigentes), 
                'cantVencidos' => sprintf("%.2f", $cantVencidos), 
                'cantTotal' => sprintf("%.2f", $countDocumentos->cantDocumentos), 
                'importePagados' => sprintf("%.2f", 0),

            ];
        }
        
        $importeVencidos = 0;
        $importeVigentes = 0;
        $importePagados = 0;

        foreach($documentosCliente as $documentosClientess){
            if($documentosClientess->documentoSaldo <= 0){
                $importePagados = $importePagados+$documentosClientess->documentoImporte;

            }else{
                $fechaVencimiento = $documentosClientess->documentoFechaVencimieto;
                $dias	= (strtotime($fechaActual)-strtotime($fechaVencimiento))/86400;
                if($dias <= 0){
                    $importeVencidos = $importeVencidos+$documentosClientess->documentoImporte;
                }else{
                    $importeVigentes = $importeVigentes+$documentosClientess->documentoImporte;
                }
            }
        }

        $sumaImportesDocumentos = documentos::select('documentos.id as documentoId')
                                        ->where('cliente_id', '=', $clienteid)
                                        ->sum("documentos.importe");

        if($sumaImportesDocumentos > 0){
            $porcentajeImporteVencido = $importeVencidos*100;
            $porcentajeImporteVencido = $porcentajeImporteVencido/$sumaImportesDocumentos;

            $porcentajeImporteVigente = $importeVigentes*100;
            $porcentajeImporteVigente = $porcentajeImporteVigente/$sumaImportesDocumentos;

            $porcentajeImportePagados = $importePagados*100;
            $porcentajeImportePagados = $porcentajeImportePagados/$sumaImportesDocumentos;

            $porcentajeImporteDocumentos = (object) [
                
                'vencido' => sprintf("%.2f", $porcentajeImporteVencido),
                'vigente' => sprintf("%.2f", $porcentajeImporteVigente),
                'pagado' => sprintf("%.2f", $porcentajeImportePagados),
                'importeVencido' =>sprintf("%.2f", $importeVencidos),
                'importeVigente' => sprintf("%.2f", $importeVigentes),
                'importePagado' => sprintf("%.2f", $importePagados),
                'importeTotal' => sprintf("%.2f", $sumaImportesDocumentos),
            ];

        }else{

            $porcentajeImporteDocumentos = (object) [
                'vencido' => sprintf("%.2f", 0),
                'vigente' => sprintf("%.2f", 0),
                'pagado' => sprintf("%.2f", 0),
                'importeVencido' => sprintf("%.2f", $importeVencidos),
                'importeVigente' => sprintf("%.2f", $importeVigentes),
                'importePagado' => sprintf("%.2f", $importePagados),
                'importeTotal' => sprintf("%.2f", $sumaImportesDocumentos),
                
            ];

        }
        
        if (sizeof($tramosSocio) > 0){
            return json_encode(array("code" => true, 
                                        "cliente"=>$clienteSocio, 
                                        "tramos"=>$listaTramos , 
                                        "porcentaje"=>$porcentajeDocumentos,
                                        "porcentajeImportes"=>$porcentajeImporteDocumentos,
                                        "load"=>true,));
        }else{
            return json_encode(array("code" => false, 
                                        "cliente"=>$clienteSocio,  
                                        "porcentaje"=>$porcentajeDocumentos,
                                        "porcentajeImportes"=>$porcentajeImporteDocumentos,
                                        "load"=>true  ));
        }

    }
}
