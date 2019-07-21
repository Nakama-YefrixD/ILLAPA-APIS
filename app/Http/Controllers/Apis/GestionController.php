<?php

namespace App\Http\Controllers\Apis;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\empresas;
use App\socios;
use App\clientes;
use App\documentos;
use App\sectores;
use App\acciones;
use App\telefonos;
use App\direcciones;
use DB;

class GestionController extends Controller
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
        $empresas = empresas::select("empresas.nombre as empresaNombre",
                                        "empresas.id as empresaId",
                                        "p.imagen as personaImagen", 
                                        DB::raw('count(d.id) as countDocumentos'))
                                        
                                    ->leftJoin('users as u', 'u.id', '=', 'empresas.correo_id')
                                    ->leftJoin('personas as p', 'p.id', '=', 'u.persona_id')
                                    ->leftJoin('socios as s', 's.empresa_id', '=', 'empresas.id')
                                    ->leftJoin('sectores as sct', 'sct.socio_id', '=', 's.id')
                                    ->leftJoin('clientes as c', 'c.sector_id', '=', 'sct.id')
                                    ->leftJoin('documentos as d', 'd.cliente_id', '=', 'c.id')
                                    ->where('empresas.estado', '=', 1)
                                    ->groupBy('empresas.id')
                                    ->get();

        $listaEmpresas = array(
            array(
                'empresaNombre' => 0,
                'empresaId' => 0,
                'personaImagen' => 0,
                'countDocumentos' => 0,
                'countDocumentosVencidos' => 0,
                
            ),
            
        );


        $cont = 0;
        foreach($empresas as $empresass){

            $listaEmpresas[$cont]['empresaNombre'] = $empresass->empresaNombre;
            $listaEmpresas[$cont]['empresaId'] = $empresass->empresaId;
            $listaEmpresas[$cont]['personaImagen'] = $empresass->personaImagen;
            $listaEmpresas[$cont]['countDocumentos'] = $empresass->countDocumentos;
            

            $empresasDocumentosVencidas = empresas::select( DB::raw('count(d.id) as countDocumentosVencidos') )
                                                    ->leftJoin('users as u', 'u.id', '=', 'empresas.correo_id')
                                                    ->leftJoin('personas as p', 'p.id', '=', 'u.persona_id')
                                                    ->leftJoin('socios as s', 's.empresa_id', '=', 'empresas.id')
                                                    ->leftJoin('sectores as sct', 'sct.socio_id', '=', 's.id')
                                                    ->leftJoin('clientes as c', 'c.sector_id', '=', 'sct.id')
                                                    ->leftJoin('documentos as d', 'd.cliente_id', '=', 'c.id')
                                                    ->where('empresas.id', '=', $empresass->empresaId)
                                                    ->where('d.fechavencimiento', '>', $fechaActual)
                                                    ->groupBy('empresas.id')
                                                    ->first();
            $contDocumentosVencidos = 0;
            if($empresasDocumentosVencidas['countDocumentosVencidos'] != null){
                $contDocumentosVencidos = $empresasDocumentosVencidas['countDocumentosVencidos'];
            }
            $listaEmpresas[$cont]['countDocumentosVencidos'] = $contDocumentosVencidos;
            $cont = $cont+1;
        }

        
        $documentos = documentos::where('fechavencimiento', '>', $fechaActual)->count();
        

                        
        if (sizeof($empresas) > 0){
            return json_encode(array("code" => true, "result"=>$listaEmpresas, "numeroVencidos" => $documentos,"load"=> true ));
        }else{
            return json_encode(array("code" => false,  "load"=>true ));
        }

    }

    public function mostrarSocios($empresaid)
    {

        $fechaActual = date('Y-m-d');
        $empresa = empresas::select("empresas.nombre as nombre", 
                                        "p.imagen as personaImagen", 
                                        DB::raw('count(d.id) as countDocumentos'))
                                        
                                    ->leftJoin('users as u', 'u.id', '=', 'empresas.correo_id')
                                    ->leftJoin('personas as p', 'p.id', '=', 'u.persona_id')
                                    ->leftJoin('socios as s', 's.empresa_id', '=', 'empresas.id')
                                    ->leftJoin('sectores as sct', 'sct.socio_id', '=', 's.id')
                                    ->leftJoin('clientes as c', 'c.sector_id', '=', 'sct.id')
                                    ->leftJoin('documentos as d', 'd.cliente_id', '=', 'c.id')
                                    ->where('empresas.id', '=', $empresaid)
                                    ->groupBy('empresas.id')
                                    ->first();

        $documentosVencidos = empresas::select(DB::raw('count(d.id) as countDocumentosVencidos'))
                                            
                                        ->leftJoin('users as u', 'u.id', '=', 'empresas.correo_id')
                                        ->leftJoin('personas as p', 'p.id', '=', 'u.persona_id')
                                        ->leftJoin('socios as s', 's.empresa_id', '=', 'empresas.id')
                                        ->leftJoin('sectores as sct', 'sct.socio_id', '=', 's.id')
                                        ->leftJoin('clientes as c', 'c.sector_id', '=', 'sct.id')
                                        ->leftJoin('documentos as d', 'd.cliente_id', '=', 'c.id')
                                        ->where('empresas.id', '=', $empresaid)
                                        ->where('d.fechavencimiento', '>', $fechaActual)
                                        ->first();
                                        

        $sociosEmpresa = socios::select("socios.id as socioId", "socios.empresa_id as empresaId", 
                                        "socios.estado as socioEstado", "p.nombre as personaNombre", 
                                        "p.imagen as personaImagen", DB::raw('count(d.id) as countDocumentos'))
                            ->leftjoin('users as u', 'u.id', '=', 'socios.correo_id')
                            ->leftjoin('personas as p', 'p.id', '=', 'u.persona_id')
                            ->leftJoin('sectores as sct', 'sct.socio_id', '=', 'socios.id')
                            ->leftJoin('clientes as c', 'c.sector_id', '=', 'sct.id')
                            ->leftJoin('documentos as d', 'd.cliente_id', '=', 'c.id')
                            ->where('socios.empresa_id', '=', $empresaid)
                            ->groupBy('socios.id')
                            ->get();

         $listSociosEmpresa = array(
            array(
                'socioId' => 0,
                'empresaId' => 0,
                'socioEstado' => 0,
                'personaNombre' => 0,
                'personaImagen' => 0,
                'countDocumentos' => 0,
                'countDocumentosVencidos' => 0,
                
            ),
            
        );


        $cont = 0;
        foreach($sociosEmpresa as $sociosEmpresas){

            $listSociosEmpresa[$cont]['socioId'] = $sociosEmpresas->socioId;
            $listSociosEmpresa[$cont]['empresaId'] = $sociosEmpresas->empresaId;
            $listSociosEmpresa[$cont]['socioEstado'] = $sociosEmpresas->socioEstado;
            $listSociosEmpresa[$cont]['personaNombre'] = $sociosEmpresas->personaNombre;
            $listSociosEmpresa[$cont]['personaImagen'] = $sociosEmpresas->personaImagen;
            $listSociosEmpresa[$cont]['countDocumentos'] = $sociosEmpresas->countDocumentos;
            
            $sociosEmpresaDocumentosVencidos = socios::select(DB::raw('count(d.id) as countDocumentosVencidos'))
                                                        ->leftjoin('users as u', 'u.id', '=', 'socios.correo_id')
                                                        ->leftjoin('personas as p', 'p.id', '=', 'u.persona_id')
                                                        ->leftJoin('sectores as sct', 'sct.socio_id', '=', 'socios.id')
                                                        ->leftJoin('clientes as c', 'c.sector_id', '=', 'sct.id')
                                                        ->leftJoin('documentos as d', 'd.cliente_id', '=', 'c.id')
                                                        ->where('socios.empresa_id', '=', $empresaid)
                                                        ->where('d.fechavencimiento', '>', $fechaActual)
                                                        ->groupBy('socios.id')
                                                        ->first();

            $contDocumentosVencidos = 0;
            if($sociosEmpresaDocumentosVencidos['countDocumentosVencidos'] != null){
                $contDocumentosVencidos = $sociosEmpresaDocumentosVencidos['countDocumentosVencidos'];
            }
            $listSociosEmpresa[$cont]['countDocumentosVencidos'] = $contDocumentosVencidos;
            $cont = $cont+1;
        }

        if (sizeof($sociosEmpresa) > 0){
            return json_encode(array("code" => true,  "empresa"=>$empresa, 
                                        "numeroVencido"=>$documentosVencidos, "result"=>$listSociosEmpresa, "load"=>true  ));
        }else{
            return json_encode(array("code" => false, "empresa"=>$empresa, "load"=>true));
        }

    }


    public function mostrarClientes($socioId)
    {

        $fechaActual = date('Y-m-d');
        $socioEmpresa = socios::select("socios.id as socioId", "socios.empresa_id as empresaId", 
                                        "socios.estado as socioEstado", "p.nombre as personaNombre", 
                                        "p.imagen as personaImagen", 
                                        DB::raw('count(d.id) as countDocumentos'))
                                        
                                    ->leftJoin('users as u', 'u.id', '=', 'socios.correo_id')
                                    ->leftJoin('personas as p', 'p.id', '=', 'u.persona_id')
                                    ->leftJoin('sectores as sct', 'sct.socio_id', '=', 'socios.id')
                                    ->leftJoin('clientes as c', 'c.sector_id', '=', 'sct.id')
                                    ->leftJoin('documentos as d', 'd.cliente_id', '=', 'c.id')
                                    ->where('socios.id', '=', $socioId)
                                    ->groupBy('socios.id')
                                    ->first();

        $documentosVencidos = socios::select(DB::raw('count(d.id) as countDocumentosVencidos'))
                                            
                                        ->leftJoin('users as u', 'u.id', '=', 'socios.correo_id')
                                        ->leftJoin('personas as p', 'p.id', '=', 'u.persona_id')
                                        ->leftJoin('sectores as sct', 'sct.socio_id', '=', 'socios.id')
                                        ->leftJoin('clientes as c', 'c.sector_id', '=', 'sct.id')
                                        ->leftJoin('documentos as d', 'd.cliente_id', '=', 'c.id')
                                        ->where('socios.id', '=', $socioId)
                                        ->where('d.fechavencimiento', '>', $fechaActual)
                                        ->first();

        

        // select c.estado, s.id, p.nombre
        // from clientes c, sectores sct, sectoristas scts, socios s, users u, personas p
        // where c.sector_id = sct.id && sct.sectorista_id = scts.id && scts.socio_id = s.id && u.id = c.correo_id && u.persona_id = p.id ;        
        $clientesSocio = clientes::select('sct.id as sectorId' ,'clientes.estado as clientesEstado', 
                                            'clientes.id as clienteId',
                                            's.id as socioId', 'p.nombre as personaNombre', 
                                            "p.imagen as personaImagen", DB::raw('count(d.id) as countDocumentos'))

                            ->leftjoin('users as u', 'u.id', '=', 'clientes.correo_id')
                            ->leftjoin('personas as p', 'p.id', '=', 'u.persona_id')
                            ->leftJoin('sectores as sct', 'sct.id', '=', 'clientes.sector_id')
                            ->leftJoin('socios as s', 's.id', '=', 'sct.socio_id')
                            ->leftJoin('documentos as d', 'd.cliente_id', '=', 'clientes.id')
                            ->where('s.id', '=', $socioId)
                            ->groupBy('clientes.id')
                            ->get();

                            
        

        $listClientesSocio = array(
            array(
                'sectorId' => 0,
                'clientesEstado' => 0,
                'clienteId' => 0,
                'socioId' => 0,
                'personaNombre' => 0,
                'personaImagen' => 0,
                'countDocumentos' => 0,
                'countDocumentosVencidos' => 0,
                
            ),
            
        );


        $cont = 0;
        foreach($clientesSocio as $clientesSocios){

            $listClientesSocio[$cont]['sectorId'] = $clientesSocios->sectorId;
            $listClientesSocio[$cont]['clientesEstado'] = $clientesSocios->clientesEstado;
            $listClientesSocio[$cont]['clienteId'] = $clientesSocios->clienteId;
            $listClientesSocio[$cont]['socioId'] = $clientesSocios->socioId;
            $listClientesSocio[$cont]['personaNombre'] = $clientesSocios->personaNombre;
            $listClientesSocio[$cont]['personaImagen'] = $clientesSocios->personaImagen;
            $listClientesSocio[$cont]['countDocumentos'] = $clientesSocios->countDocumentos;
            
            
            $clienteSocio = clientes::select(DB::raw('count(d.id) as countDocumentosVencidos'))

                            ->leftjoin('users as u', 'u.id', '=', 'clientes.correo_id')
                            ->leftjoin('personas as p', 'p.id', '=', 'u.persona_id')
                            ->leftJoin('sectores as sct', 'sct.id', '=', 'clientes.sector_id')
                            ->leftJoin('socios as s', 's.id', '=', 'sct.socio_id')
                            ->leftJoin('documentos as d', 'd.cliente_id', '=', 'clientes.id')
                            ->where('clientes.id', '=', $clientesSocios->clienteId)
                            ->where('d.fechavencimiento', '>', $fechaActual)
                            ->groupBy('clientes.id')
                            ->first();


            

            $contDocumentosVencidos = 0;
            if($clienteSocio['countDocumentosVencidos'] != null){
                $contDocumentosVencidos = $clienteSocio['countDocumentosVencidos'];
            }
            $listClientesSocio[$cont]['countDocumentosVencidos'] = $contDocumentosVencidos;
            $cont = $cont+1;
        }

        if (sizeof($clientesSocio) > 0){
            return json_encode(array("code" => true, "result"=>$listClientesSocio, 
                                            "socio"=>$socioEmpresa , "numeroVencido"=>$documentosVencidos,"load"=>true  ));
        }else{
            return json_encode(array("code" => false, "socio"=>$socioEmpresa , "load"=>true));
        }
    }

    public function mostrarSectores($socioId)
    {
        $sectores = sectores::where('socio_id', '=', $socioId)
                            ->get();

        return json_encode(array("code" => true, "sectores"=>$sectores, "load"=>true ));
    }

    public function filtroClientesSector(Request $request)
    {
        // $sectorId = $request->sectorId;
        // WHERE (gender = 'Male' and age >= 18) or (gender = 'Female' and age >= 65)
        
        $sectores = clientes::where('sector_id', '=', 1)
                             ->get();

        $otros = clientes::where('sector_id', '=', 6)
                             ->get();

        
        // $merged = (object) array_merge((array) $sectores, (array) $sectoress);
        $result = (object) array_merge( (object) $sectores, (object) $otros);

        return json_encode($result);
    }

    public function mostrarDocumentosCliente($clienteId)
    {

        $clienteSocio = clientes::select('clientes.estado as clientesEstado', 's.id as socioId', 
                                            'p.nombre as personaNombre', "clientes.imagen as personaImagen",
                                            "p.tipoDocumentoIdentidad_id as personaTipoIdentificacion",
                                            "tdi.nombre as tipoDocumentoIdentidad",
                                            'p.numeroidentificacion as personaNumeroIdentificacion')

                            ->join('sectores as sct', 'sct.id', '=', 'clientes.sector_id')
                            ->join('socios as s', 's.id', '=', 'sct.socio_id')

                            ->join('users as u', 'u.id', '=', 'clientes.correo_id')
                            ->join('personas as p', 'p.id', '=', 'u.persona_id')
                            ->join('tiposDocumentosIdentidad as tdi', 'tdi.id', '=', 'p.tipoDocumentoIdentidad_id')
                            ->where('clientes.id', '=', $clienteId)
                            ->first();

        $documentosCliente = documentos::select("td.nombre as tipoDocumentoIdentidad", 'documentos.numero as numero',
                                                'documentos.fechavencimiento as fechavencimiento', 
                                                'documentos.importe as importe', 'documentos.saldo as saldo',
                                                'documentos.id as id', 'tm.nombre as moneda')
                                        ->join('tiposDocumentos as td','td.id', '=', 'documentos.tipoDocumento_id' )
                                        ->join('tiposMonedas as tm','tm.id', '=', 'documentos.tipoMoneda_id' )
                                        ->where('cliente_id', '=', $clienteId)
                                        ->get();

        $documentosClienteOrdenado = documentos::select("td.nombre as tipoDocumentoIdentidad", 'documentos.numero as numero',
                                                'documentos.fechavencimiento as fechavencimiento', 
                                                'documentos.importe as importe', 'documentos.saldo as saldo',
                                                'documentos.id as id', 'tm.nombre as moneda')
                                        ->join('tiposDocumentos as td','td.id', '=', 'documentos.tipoDocumento_id' )
                                        ->join('tiposMonedas as tm','tm.id', '=', 'documentos.tipoMoneda_id' )
                                        ->where('cliente_id', '=', $clienteId)
                                        ->orderby('documentos.fechavencimiento')
                                        ->get();

        $pagosDocumento = documentos::select('documentos.id as documentosId','p.documento_id as pagosDocumentoId', 
                                               'p.tipoPago_id as pagosTipo', 'p.numero as pagosNumero', 
                                               'tp.nombre as tipo',
                                                'p.fechaemision as pagosFechaEmision', 
                                                'p.fechavencimiento as pagosFechaVencimiento', 
                                                'p.importe as pagosImporte', 'p.saldo as pagosSaldo')
                                    
                                    ->leftJoin('pagos as p', 'documentos.id', '=', 'p.documento_id')
                                    ->leftJoin('tiposPagos as tp', 'tp.id', '=', 'p.tipoPago_id')                                
                                    ->where('documentos.cliente_id', '=', $clienteId)
                                    ->get();

        

        if (sizeof($documentosCliente) > 0){
            return json_encode(array("code" => true, 
                                        "result"=>$documentosCliente, 
                                        "resultOrdenados"=>$documentosClienteOrdenado, 
                                        "pagos"=>$pagosDocumento ,
                                        "cliente"=>$clienteSocio , 
                                        "load"=>true ));
        }else{
            return json_encode(array("code" => false,  "cliente"=>$clienteSocio , "load"=>true));
        }

    }

    public function formularAccionCliente($clienteId)
    {

        $clienteSocio = clientes::select('clientes.estado as clientesEstado', 's.id as socioId', 
                                            'p.nombre as personaNombre', "p.imagen as personaImagen",
                                            'p.tipoidentificacion as personaTipoIdentificacion',
                                            'p.numeroidentificacion as personaNumeroIdentificacion')

                            ->join('sectores as sct', 'sct.id', '=', 'clientes.sector_id')
                            ->join('socios as s', 's.id', '=', 'sct.socio_id')
                            ->join('users as u', 'u.id', '=', 'clientes.correo_id')
                            ->join('personas as p', 'p.id', '=', 'u.persona_id')
                            ->where('clientes.id', '=', $clienteId)
                            ->first();

        $documentosCliente = documentos::where('cliente_id', '=', $clienteId)
                                        ->get();

        

        if (sizeof($documentosCliente) > 0){
            return json_encode(array("code" => true, "result"=>$documentosCliente, "cliente"=>$clienteSocio , "load"=>true ));
        }else{
            return json_encode(array("code" => false,  "cliente"=>$clienteSocio , "load"=>true));
        }

    }
    

    public function datosCliente($idCliente)
    {
        $telefonosCliente = telefonos::where('cliente_id', '=', $idCliente)
                                        ->get();
        
        $direcciones = direcciones::where('cliente_id', '=', $idCliente)
                                         ->get();

        $acciones = acciones::select('acciones.created_at as created_at', 'acciones.descripcion as descripcion',
                                        'ta.nombre as tipoAccion', 'acciones.fechacompromiso', 'acciones.importecompromiso',
                                        'acciones.fechaprorroga', 'acciones.fechahoraalarma' )
                            ->join('tiposAcciones as ta', 'ta.id', '=','acciones.tipoAccion_id')
                            ->where('cliente_id', '=', $idCliente)
                            ->orderby('acciones.created_at', 'desc')
                            ->get();
        
        if(sizeof($telefonosCliente) > 0 &&  sizeof($acciones) > 0 && sizeof($direcciones) > 0){
            
            return json_encode(array("code" => true, "telefonos"=>$telefonosCliente, "direcciones"=>$direcciones ,"acciones" =>$acciones ,"load"=>true ));
            
        }else if(sizeof($telefonosCliente) > 0 &&  sizeof($acciones) > 0){
            // return json_encode(array("code" => false, "codeDireccionesTelefonos" => true, "telefonos"=>$telefonosCliente,"acciones" =>$acciones ,"load"=>true ));
            return json_encode(array("code" => false,  "telefonos"=>$telefonosCliente,"acciones" =>$acciones ,"load"=>true ));
        }else if(sizeof($direcciones) > 0 && sizeof($acciones) > 0){
            return json_encode(array("code" => false, "codeDireccionesAcciones" => true, "direcciones"=>$direcciones,"acciones" =>$acciones ,"load"=>true ));
        }else if(sizeof($direcciones) > 0 && sizeof($telefonosCliente) > 0){
            return json_encode(array("code" => false, "codeDireccionesTelefonos" => true, "direcciones"=>$direcciones, "telefonos"=>$telefonosCliente ,"load"=>true ));
        }else if(sizeof($telefonosCliente) > 0){
            return json_encode(array("code" => false, "codeTelefono" => true, "telefonos"=>$telefonosCliente, "load"=>true ));
        }else if(sizeof($acciones) > 0){
            return json_encode(array("code" => false, "codeAcciones" => true, "acciones" =>$acciones, "load"=>true ));
        }else if(sizeof($direcciones) > 0){
            return json_encode(array("code" => false, "codeDirecciones" => true, "direcciones"=>$direcciones, "load"=>true ));
        }else{
            return json_encode(array("code" => false, "codeNada" => true, "load"=>true ));
        }


    }

    public function agregarAccion(Request $request)
    {

        $clienteId = $request->clienteId;
        $tipoAccion = $request->tipoAccion;
        $descripcion = $request->descripcion;
        $fechaActualSistema = $request->fechaActualSistema;
        
        // $idDocumento = $request->documentoId;
        $compromisoPago = $request->compromisoPago;
        $fechaCompromiso = $request->fechaCompromiso;
        // $documentoCompromiso = $request->documentoCompromiso;
        $importeCompromiso = $request->importeCompromiso;
        
        $prorroga = $request->prorroga;
        $fechaProrroga = $request->fechaProrroga;
        
        $alerta = $request->alerta;
        $fechaAlerta = $request->fechaAlerta;
        // $horaAlerta = $request->horaAlerta;
        // $fechaHora = $fechaAlerta+' '+$horaAlerta;
        

        
        
        
        if($compromisoPago == "true"){
            $compromisoPago = 1;
        }else{
            // $idDocumento = null;
            $fechaCompromiso = null;
            $importeCompromiso = null;
            $compromisoPago = 0;

        }

        if($prorroga == "true"){
            $prorroga = 1;

        }else{
            $prorroga = 0;
            $fechaProrroga = null;
        }

        if($alerta == "true"){
            $alerta = 1;
        }else{
            $alerta = 0;
            $fechaAlerta = null;
        }

        $accionCliente = new acciones;
        $accionCliente->cliente_id = $clienteId;
        $accionCliente->tipoAccion_id = $tipoAccion;
        
        // $accionCliente->documento_id = $idDocumento;
        $accionCliente->descripcion = $descripcion;
        $accionCliente->flagcompromiso = $compromisoPago;
        $accionCliente->fechacompromiso =$fechaCompromiso;
        $accionCliente->importecompromiso = $importeCompromiso;
        $accionCliente->flagprorroga = $prorroga;
        $accionCliente->fechaprorroga = $fechaProrroga;

        $accionCliente->flagalarma = $alerta;
        $accionCliente->fechahoraalarma = $fechaAlerta;  // 1970-01-01 00:00:01
        $accionCliente->estado = 1;
        
        
        $accionCliente->created_at = $fechaActualSistema;
        $accionCliente->save();

    }

   




}
