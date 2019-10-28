<?php

namespace App\Http\Controllers\Apis;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\Controller;
use DB;
use App\empresas;
use App\socios;
use App\clientes;
use App\documentos;
use App\pagos;
use App\personas;
use App\sectoristas;
use App\sectores;
use App\User;
use App\clienteDatos;

use App\telefonos;
use App\correos;
use App\direcciones;
use App\tiposMonedas;
use App\tiposDocumentos;
use App\tiposPagos;
use App\tiposDocumentosIdentidad;

use App\tiposTelefonos;

use Peru\Jne\Dni;
use Peru\Jne\DniParser;
use Peru\Sunat\Ruc;
use Peru\Http\ContextClient;



class DatoController extends Controller
{
    public function mostrarEmpresas()
    {

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
                                        "tdi.nombre as tipoDocumentoIdentidad",
                                        "p.numeroidentificacion as personaNumeroIdentificacion",
                                        "p.imagen as personaImagen")
                            ->join('users as u', 'u.id', '=', 'empresas.correo_id')
                            ->join('personas as p', 'p.id', '=', 'u.persona_id')
                            ->join('tiposDocumentosIdentidad as tdi', 'tdi.id', '=', 'p.tipoDocumentoIdentidad_id')
                            ->where('empresas.id', '=', $empresaid)
                            ->first();

        // $sociosEmpresa = socios::where('empresa_id', '=', $empresaid)->get();
        $sociosEmpresa = socios::select("socios.id as socioId", "socios.empresa_id as empresaId", 
                                        "u.email as userEmail", "tdi.nombre as tipoDocumentoIdentidad",
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
            return json_encode(array("code" => true, "result"=>$sociosEmpresa, "empresa"=>$empresa , "load"=>true ));
        }else{
            return json_encode(array("code" => false, "message"=>"No hay empresas !", "empresa"=>$empresa, "load"=>true));
        }

    }

    public function buscarClientes(Request $request)
    {
        
        $dni = $request->dni;
        $idSocioSeleccionado = $request->idSocioSeleccionado;
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
            $sectoristas = sectoristas::where('socio_id', '=', $idSocioSeleccionado)
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

    public function mostrarSectores($socioId)
    {
        $sectores = sectores::where('socio_id', '=', $socioId)
                            ->get();

        $tiposTelefonos = tiposTelefonos::select('id','nombre')
                                        ->where('estado','=',1)
                                        ->get();

        return json_encode(array("code" => true, 
                                    "sectores"=>$sectores, 
                                    "tiposTelefonos"=>$tiposTelefonos,
                                    "load"=>true ));
    }

    public function clienteDatos($idCliente, $idSocio)
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
        $sector = clientes::select('sct.id','sct.descripcion')
                            ->join('sectores as sct', 'sct.id','=','clientes.sector_id')
                            ->where('sct.socio_id','=',$idSocio)
                            ->where('clientes.id','=',$idCliente)
                            ->first();

        $sectores = sectores::where('socio_id', '=', $idSocio)
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

        if(sizeof($sectores) > 0){
            $codeSectores = true;
        }else{
            $codeSectores = false;
        }

        return json_encode(array(
            "load"=>true,
            "codeTelefonos"=>$codeTelefonos,
            "telefonos" => $telefonos,
            "codeDirecciones"=>$codeDirecciones,
            "direcciones"=>$direcciones,
            "codeCorreos"=>$codeCorreos,
            "correos"=>$correos,
            "codeSectores"=>$codeSectores,
            "sectores"=>$sectores,
            "sector"=>$sector,
            "tiposTelefonos"=>$tiposTelefonos

        ));

    }

    public function agregarNuevoCliente(Request $request)
    {
        $tipoIdentidad = $request->tipoIdentidad;
        $dni = $request->dni;
        $nombre = $request->nombre;
        $idSector = $request->idSector;

        $ubicacion = 'imagenes_clientes/clientes.png';
        $siPersona = personas::where('numeroidentificacion', '=', $dni)
                                ->first();
        if($siPersona){

            $personaId = $siPersona->id;
            $exisUser = User::where('persona_id', '=', $personaId )
                                            ->first();
            if($exisUser){
                $userId = $exisUser->id;
            }else{
                $userCliente = new User;
                $userCliente->persona_id = $personaId;
                $userCliente->email = null;
                $userCliente->estado = 0;
                $userCliente->email_verified_at = null;
                $userCliente->password = Hash::make('illapa123');
                $userCliente->api_token = Str::random(60);
                
                $userCliente->save();
                $userId = $userCliente->id;
            }

        }else{

            $personas = new personas;
            $personas->tipoDocumentoIdentidad_id = $tipoIdentidad;
            $personas->numeroidentificacion = $dni;
            $personas->nombre = $nombre;
            $personas->imagen = 'https://cdn.pixabay.com/photo/2015/03/04/22/35/head-659651_960_720.png';
            $personas->estado = 1;
            $personas->save();

            $personaId = $personas->id;
            
            $userCliente = new User;
            $userCliente->persona_id = $personaId;
            $userCliente->email = null;
            $userCliente->estado = 0;
            $userCliente->email_verified_at = null;
            $userCliente->password = Hash::make('illapa123');
            $userCliente->api_token = Str::random(60);
            
            $userCliente->save();
            $userId = $userCliente->id;

        }
        
        

        $cliente = new clientes;
        $cliente->sector_id = $idSector;
        $cliente->correo_id = $userId;
        $cliente->imagen = $ubicacion;
        $cliente->estado = 1;
        $cliente->save();
        $idCliente = $cliente->id;

        $contDirecciones = $request->contDirecciones;
        $contTelefonos = $request->contTelefonos;
        $contCorreos = $request->contCorreos;

        // $idSocio = $request->idSocio; ID DEL SOCIO !! DEL CLIENTE
        
        for($x = 0; $x < $contDirecciones; $x++){
            $datorecibir = "direccion".$x;
            $direccionCiudad = "direccionCiudad".$x;
            $direccionPostal = "direccionCodigoPostal".$x;
            $direccionPais = "direccionPais".$x;
            $direccionLatitud = "direccionLatitud".$x;
            $direccionLongitud = "direccionLongitud".$x;

            $direcciones = new direcciones;
            $direcciones->cliente_id = $idCliente;
            $direcciones->correo_id = $userId;
            $direcciones->calle = $request->$datorecibir;
            $direcciones->ciudad = $request->$direccionCiudad;
            $direcciones->codigopostal = $request->$direccionPostal;
            $direcciones->pais = $request->$direccionPais;
            $direcciones->latitud = $request->$direccionLatitud;
            $direcciones->longitud = $request->$direccionLongitud;

            $direcciones->estado = 1;
            
            if($direcciones->save()){
               
            }

        }
        for($y = 0; $y < $contTelefonos; $y++){
            $datorecibir = "telefono".$y;
            $telefonoPrefijo = "telefonoPrefijo".$y;
            $telefonoTipo = "telefonoTipo".$y;
            $telefonoPais = "telefonoPais".$y;

            if($request->$datorecibir != null){
                $telefonos = new telefonos;
                $telefonos->cliente_id = $idCliente;
                $telefonos->correo_id = $userId;
                $telefonos->pais = $request->$telefonoPais;
                $telefonos->prefijo = $request->$telefonoPrefijo;
                $telefonos->numero = $request->$datorecibir;
                $telefonos->tipotelefono_id = $request->$telefonoTipo;
                $telefonos->estado = 1;
                if($telefonos->save()){
                    
                }
            }
            
            
        }
        for($z = 0; $z < $contCorreos; $z++){
            $datorecibir = "correo".$z;
            if($request->$datorecibir != null){
                $correos = new correos;
                $correos->cliente_id = $idCliente;
                $correos->correo_id = $userId;
                $correos->correo = $request->$datorecibir;
                $correos->estado = 1;
                if($correos->save()){

                }
            }
            
        }
       
        return json_encode(array("estado" => true, "idClienteNuevo"=>$idCliente ));

    }

    public function agregarDocumento(Request $request)
    {
        $clienteId = $request->idCliente;
        $tipo = $request->tipoDocumento;
        $nombre = $request->nombreDocumento;
        $fechaEmision = $request->emisionDocumento;
        $fechaVencimiento = $request->vencimientoDocumento;
        $moneda = $request->monedaDocumento;
        $importe = $request->importeDocumento;

        $documento = new documentos;
        $documento->cliente_id = $clienteId;
        $documento->tipoDocumento_id = $tipo;
        $documento->numero = $nombre;
        $documento->fechaemision = $fechaEmision;
        $documento->fechavencimiento = $fechaVencimiento;
        $documento->tipoMoneda_id = $moneda;
        $documento->importe = $importe;
        $documento->saldo = $importe;
        $documento->estado = 1;
        $documento->save();

        if($documento->save()){
            return json_encode(array("code" => true, "load"=>true ));
        }else{
            return json_encode(array("code" => false,  "load"=>true ));
        }

    }

    public function agregarPago(Request $request)
    {
        $documentoId = $request->documentoId;
        $tipo = $request->tipoPago;
        $numero = $request->numeroPago;
        $fechaEmision = $request->emisionPago;
        $fechaVencimiento = $request->vencimientoPago;
        $moneda = $request->monedaPago;
        $importe = $request->importePago;
        $saldoDocumento = $request->saldoDocumento;

        $nuevoSaldoDocumento = $saldoDocumento - $importe;

        $documento = documentos::find($documentoId);
        $documento->saldo = $nuevoSaldoDocumento;
        $documento->update();

        $pago = new pagos;
        $pago->documento_id = $documentoId;
        $pago->tipoPago_id = $tipo;
        $pago->numero = $numero;
        $pago->fechaemision = $fechaEmision;
        $pago->fechavencimiento = $fechaEmision;
        $pago->moneda = $moneda;
        $pago->importe = $importe;
        $pago->saldo = $nuevoSaldoDocumento;
        $pago->estado = 1;
        $pago->save();

        if($pago->save()){
            return json_encode(array("code" => true, "load"=>true ));
        }else{
            return json_encode(array("code" => false,  "load"=>true ));
        }
      

    }

    public function editarPago(Request $request)
    {
        $documentoId        = $request->documentoId;
        $pagoId             = $request->pagoId;
        $tipo               = $request->tipoPago;
        $numero             = $request->numeroPago;
        $fechaEmision       = $request->emisionPago;
        $importe            = $request->importePago;
        $saldoDocumento     = $request->saldoDocumento;


        $pago                       = pagos::find($pagoId);
        $anteriorPago               = $pago->importe;

        $documentoAnterior              = documentos::find($documentoId);
        $anteriorSaldoDocumento         = $documentoAnterior->saldo;
        $recuperarSaldoSinPagoDocumento = $anteriorPago + $anteriorSaldoDocumento;
        $documentoAnterior->saldo       = $recuperarSaldoSinPagoDocumento;
        $documentoAnterior->update();

        $nuevoSaldoDocumento        = $documentoAnterior->saldo - $importe;
        $documento                  = documentos::find($documentoId);
        $documento->saldo           = $nuevoSaldoDocumento;
        $documento->update();

        $pago                       = pagos::find($pagoId);
        $pago->tipoPago_id          = $tipo;
        $pago->numero               = $numero;
        $pago->importe              = $importe;
        $pago->fechaemision         = $fechaEmision;
        $pago->fechavencimiento     = $fechaEmision;

        if($pago->update()){
            return json_encode(array("code" => true, "load"=>true ));
        }else{
            return json_encode(array("code" => false,  "load"=>true ));
        }
    }

    public function eliminarPago(Request $request)
    {
        $documentoId        = $request->documentoId;
        $pagoId             = $request->pagoId;

        $pago                       = pagos::find($pagoId);
        $anteriorPago               = $pago->importe;

        $documentoAnterior              = documentos::find($documentoId);
        $anteriorSaldoDocumento         = $documentoAnterior->saldo;
        $recuperarSaldoSinPagoDocumento = $anteriorPago + $anteriorSaldoDocumento;
        $documentoAnterior->saldo       = $recuperarSaldoSinPagoDocumento;
        $documentoAnterior->update();

        $pago                       = pagos::find($pagoId);
        if($pago->delete()){
            return json_encode(array("code" => true, "load"=>true ));
        }else{
            return json_encode(array("code" => false,  "load"=>true ));
        }
    }

    public function editarDocumento(Request $request)
    {
        $documentoId            = $request->documentoId;
        $tipoDocumento          = $request->tipoDoc;
        $numeroDocumento        = $request->numeroDoc;
        $tipoMoneda             = $request->tipoMonedaDoc;
        $importeDocumento       = $request->importeDoc;
        $emisionDocumento       = $request->emisionDoc;
        $vencimientoDocumento   = $request->vencDoc;

        $documento                      = documentos::find($documentoId);
        $cantidadPagada                 = $documento->importe - $documento->saldo;
        $documento->tipoDocumento_id    = $tipoDocumento;
        $documento->numero              = $numeroDocumento;
        $documento->fechaemision        = $emisionDocumento;
        $documento->fechavencimiento    = $vencimientoDocumento;
        $documento->tipoMoneda_id       = $tipoMoneda;
        $documento->importe             = $importeDocumento;
        $documento->saldo               = $importeDocumento - $cantidadPagada;

        if($documento->update()){
            return json_encode(array("code" => true, "load"=>true ));
        }else{
            return json_encode(array("code" => false,  "load"=>true ));
        }
    }

    public function eliminarDocumento(Request $request)
    {
        $documentoId        = $request->documentoId;

        $pagos = pagos::where('documento_id', $documentoId);
        $pagos->delete();

        $documento  = documentos::find($documentoId);
        if($documento->delete()){
            return json_encode(array("code" => true, "load"=>true ));
        }else{
            return json_encode(array("code" => false,  "load"=>true ));
        }
    }

    public function mostrarClientes($socioId)
    {
        $tipos = tiposDocumentosIdentidad::select('id','nombre')
                                            ->where('estado', '=', 1)
                                            ->get();
        $fechaActual = date('Y-m-d');
        $socioEmpresa = socios::select("socios.id as socioId", "socios.empresa_id as empresaId", 
                                    "socios.estado as socioEstado", "p.nombre as personaNombre", 
                                    "p.imagen as personaImagen", 
                                    DB::raw('count(d.id) as numeroDocumentos'),
                                    DB::raw("SUM(d.importe) as sumaImportesDocumentos"))
                                ->leftJoin('users as u', 'u.id', '=', 'socios.correo_id')
                                ->leftJoin('personas as p', 'p.id', '=', 'u.persona_id')
                                ->leftJoin('sectores as sct', 'sct.socio_id', '=', 'socios.id')
                                ->leftJoin('clientes as c', 'c.sector_id', '=', 'sct.id')
                                ->leftJoin('documentos as d', 'd.cliente_id', '=', 'c.id')
                                ->where('socios.id', '=', $socioId)
                                ->groupBy('socios.id')
                                ->first();


        if($socioEmpresa['sumaImportesDocumentos'] == null){
            // $socioEmpresa = (object) array( 
                                        
            //                                 'socioEstado' => $socioEmpresa['socioEstado'],
            //                                 'personaNombre' => $socioEmpresa['personaNombre'],
            //                                 'personaImagen' => $socioEmpresa['personaImagen'],
            //                                 'numeroDocumentos' => $socioEmpresa['numeroDocumentos'],
            //                                 'sumaImportesDocumentos' => sprintf("%.2f", 0));
            $socioEmpresa['sumaImportesDocumentos'] = sprintf("%.2f", 0);

        }

        $socioVencido = socios::select(DB::raw('count(d.id) as numeroDocumentosVencidos'),
                                            DB::raw("SUM(d.importe) as sumaImportesDocumentosVencidos"))
                                            
                                        ->leftJoin('users as u', 'u.id', '=', 'socios.correo_id')
                                        ->leftJoin('personas as p', 'p.id', '=', 'u.persona_id')
                                        ->leftJoin('sectores as sct', 'sct.socio_id', '=', 'socios.id')
                                        ->leftJoin('clientes as c', 'c.sector_id', '=', 'sct.id')
                                        ->leftJoin('documentos as d', 'd.cliente_id', '=', 'c.id')
                                        ->where('socios.id', '=', $socioId)
                                        ->where('d.fechavencimiento', '<', $fechaActual)
                                        ->groupBy('socios.id')
                                        ->first();
        if($socioVencido == null){
            $socioVencido = (object) array('numeroDocumentosVencidos' => 0,
                                            'sumaImportesDocumentosVencidos' => sprintf("%.2f", 0));
        }
        // $sociosEmpresa = socios::where('empresa_id', '=', $empresaid)->get();
        $clientesSocio = clientes::select('clientes.estado as clientesEstado', 'clientes.id as clienteId',
                                            "u.email as userEmail", "u.id as userId", 
                                            "p.tipoDocumentoIdentidad_id as personaTipoIdentificacion",
                                            "tdi.nombre as tipoDocumentoIdentidad",
                                            "p.numeroidentificacion as personaNumeroIdentificacion",
                                            'p.nombre as personaNombre', 
                                            'clientes.imagen as personaImagen') 
                                            
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
            return json_encode(array("code" => true, 
                                        "result"=>$clientesSocio, 
                                        "socio"=>$socioEmpresa, 
                                        "socioVencido"=>$socioVencido,
                                        "tipos" => $tipos,
                                        "load"=>true ));
        }else{
            return json_encode(array("code" => false,  
                                        "socio"=>$socioEmpresa, 
                                        "socioVencido"=>$socioVencido,
                                        "tipos" => $tipos,
                                        "load"=>true));
        }

    }

    public function mostrarClienteDocumentos($clienteid)
    {

        // $sociosEmpresa = socios::where('empresa_id', '=', $empresaid)->get();
        $clienteSocio = clientes::select('clientes.estado as clientesEstado', 's.id as socioId', 
                                            'clientes.id as clienteId',
                                            "u.email as userEmail", "p.tipoDocumentoIdentidad_id as personatipoDocumentoIdentidad_id",
                                            "p.numeroidentificacion as personaNumeroIdentificacion",
                                            "tdi.nombre as tipoDocumentoIdentidad",
                                            'p.nombre as personaNombre', 'clientes.imagen as personaImagen')
                            ->join('sectores as sct', 'sct.id', '=', 'clientes.sector_id')
                            ->join('socios as s', 's.id', '=', 'sct.socio_id')

                            ->join('users as u', 'u.id', '=', 'clientes.correo_id')
                            ->join('personas as p', 'p.id', '=', 'u.persona_id')
                            ->join('tiposDocumentosIdentidad as tdi', 'tdi.id', '=', 'p.tipoDocumentoIdentidad_id')
                            ->where('clientes.id', '=', $clienteid)
                            ->first();
        
        $tipos = tiposMonedas::select('id','nombre')
                                ->where('estado', '=', 1)
                                ->get();

        $tiposDocumentos = tiposDocumentos::select('id','nombre')
                                            ->where('estado', '=', 1)
                                            ->get();


        $documentosCliente = documentos::select('documentos.id as id','td.nombre as tipo', 
                                                'td.id as idTipoDocumento','documentos.numero as numero',
                                                'documentos.fechaemision as fechaEmision',
                                                'documentos.fechavencimiento as fechavencimiento',
                                                'documentos.importe as importe', 'documentos.saldo as saldo',
                                                'tm.id as idTipoMoneda',
                                                'tm.nombre as moneda')
                                        ->join('tiposDocumentos as td','td.id', '=', 'documentos.tipoDocumento_id' )
                                        ->join('tiposMonedas as tm','tm.id', '=', 'documentos.tipoMoneda_id' )
                                        ->where('cliente_id', '=', $clienteid)
                                        ->get();

        if (sizeof($documentosCliente) > 0){
            return json_encode(array("code" => true,"tipos"=>$tipos , "tiposDocumentos"=>$tiposDocumentos, "result"=>$documentosCliente, "cliente"=>$clienteSocio , "load"=>true ));
        }else{
            return json_encode(array("code" => false,  "tipos"=>$tipos, "tiposDocumentos"=>$tiposDocumentos, "cliente"=>$clienteSocio, "load"=>true));
        }
        
            // return json_encode(array("code" => true, "cliente"=>$clienteSocio , "load"=>true ));
        

    }

    public function mostrarClienteDocumentoPagos($documentoId)
    {

        $tiposPagos = tiposPagos::select('id','nombre')
                                ->where('estado', '=', 1)
                                ->get();
                                            
        $documentoCliente = documentos::select('documentos.cliente_id as cliente_id','documentos.id as id','td.nombre as tipo', 'documentos.numero as numero',
                                                'documentos.fechavencimiento as fechavencimiento',
                                                'documentos.importe as importe', 'documentos.saldo as saldo',
                                                'tm.nombre as moneda')
                                        ->join('tiposDocumentos as td','td.id', '=', 'documentos.tipoDocumento_id' )
                                        ->join('tiposMonedas as tm','tm.id', '=', 'documentos.tipoMoneda_id' )
                                        ->where('documentos.id', '=', $documentoId)
                                        ->first();

        
        $clienteDocumento = clientes::select('clientes.estado as clientesEstado', 's.id as socioId', 
                                            'clientes.id as clienteId',
                                            "u.email as userEmail", "tdi.nombre as personatipoDocumentoIdentidad_id",
                                            "p.numeroidentificacion as personaNumeroIdentificacion",
                                            'p.nombre as personaNombre', 'clientes.imagen as personaImagen')
                                            
                            ->join('sectores as sct', 'sct.id', '=', 'clientes.sector_id')
                            ->join('socios as s', 's.id', '=', 'sct.socio_id')

                            ->join('users as u', 'u.id', '=', 'clientes.correo_id')
                            ->join('personas as p', 'p.id', '=', 'u.persona_id')
                            ->join('tiposDocumentosIdentidad as tdi', 'tdi.id', '=', 'p.tipoDocumentoIdentidad_id')
                            ->where('clientes.id', '=', $documentoCliente->cliente_id)
                            ->first();


        $pagosDocumento = pagos::select(    'pagos.id as idPago',
                                            'tp.id as idTipoPago',
                                            'tp.nombre as tipo',
                                            'pagos.numero as numero', 
                                            'pagos.fechavencimiento as fechavencimiento', 
                                            'pagos.importe as importe')
                                ->join('tiposPagos as tp', 'tp.id', '=', 'pagos.tipoPago_id')                                
                                ->where('documento_id', '=', $documentoId)
                                ->get();  
        

        $tipos = tiposMonedas::select('id','nombre')
                                ->where('estado', '=', 1)
                                ->get();

        $tiposDocumentos = tiposDocumentos::select('id','nombre')
                                            ->where('estado', '=', 1)
                                            ->get();
        if (sizeof($pagosDocumento) > 0){

            return json_encode(array("code" => true, 
                                    "result"=>$pagosDocumento, 
                                    "tipoPagos" => $tiposPagos,
                                    "cliente"=>$clienteDocumento, 
                                    "documento"=>$documentoCliente,
                                    "tipos"=>$tipos , 
                                    "tiposDocumentos"=>$tiposDocumentos,
                                    "load"=>true ));
        }else{

            return json_encode(array("code" => false, 
                                    "cliente"=>$clienteDocumento,
                                    "tipoPagos" => $tiposPagos,
                                    "documento"=>$documentoCliente, 
                                    "tipos"=>$tipos , 
                                    "tiposDocumentos"=>$tiposDocumentos,
                                    "load"=>true));
        }
        

    }

    public function editarCliente(Request $request)
    {
        $idCliente = $request->idCliente;
        $userId = $request->userId;
        $contDirecciones = $request->contDirecciones;
        $contTelefonos = $request->contTelefonos;
        $contCorreos = $request->contCorreos;
        $sectorId = $request->sectorId;

        if($sectorId != null){
            $cliente = clientes::find($idCliente);
            $cliente->sector_id = $sectorId;
            $cliente->update();
        }
       
        
        // $idSocio = $request->idSocio; ID DEL SOCIO !! DEL CLIENTE
        
        for($x = 0; $x < $contDirecciones; $x++){
            $datorecibir = "direccion".$x;
            $direccionCiudad = "direccionCiudad".$x;
            $direccionPostal = "direccionCodigoPostal".$x;
            $direccionPais = "direccionPais".$x;
            $direccionLatitud = "direccionLatitud".$x;
            $direccionLongitud = "direccionLongitud".$x;


            $direcciones = new direcciones;
            $direcciones->cliente_id = $idCliente;
            $direcciones->correo_id = $userId;
            $direcciones->calle = $request->$datorecibir;
            $direcciones->ciudad = $request->$direccionCiudad;
            $direcciones->codigopostal = $request->$direccionPostal;
            $direcciones->pais = $request->$direccionPais;
            $direcciones->latitud = $request->$direccionLatitud;
            $direcciones->longitud = $request->$direccionLongitud;

            $direcciones->estado = 1;
            if($direcciones->save()){
               
            }



        }
        for($y = 0; $y < $contTelefonos; $y++){
            $datorecibir = "telefono".$y;
            $telefonoPrefijo = "telefonoPrefijo".$y;
            $telefonoTipo = "telefonoTipo".$y;
            $telefonoPais = "telefonoPais".$y;

            if($request->$datorecibir != null){
                $telefonos = new telefonos;
                $telefonos->cliente_id = $idCliente;
                $telefonos->pais = $request->$telefonoPais;
                $telefonos->correo_id = $userId;
                $telefonos->prefijo = $request->$telefonoPrefijo;
                $telefonos->numero = $request->$datorecibir;
                $telefonos->tipotelefono_id = $request->$telefonoTipo;
                $telefonos->estado = 1;
                if($telefonos->save()){
                    
                }
            }
            


        }
        for($z = 0; $z < $contCorreos; $z++){
            $datorecibir = "correo".$z;
            if($request->$datorecibir != null){
                $correos = new correos;
                $correos->cliente_id = $idCliente;
                $correos->correo_id = $userId;
                $correos->correo = $request->$datorecibir;
                $correos->estado = 1;
                if($correos->save()){
                    
                }
            }
            


        }
       
        return json_encode(array("estado" => true ));

    }

    public function eliminarDireccion(Request $request){
        
        $direccion = direcciones::find($request->id);
        if($direccion->delete()){
            return json_encode(array("code"=>true ));
        }else{
            return json_encode(array("code"=>false ));
        }
    }

    public function eliminarTelefonos(Request $request){
        $telefonos = telefonos::find($request->id);
        if($telefonos->delete()){
            return json_encode(array("code"=>true ));
        }else{
            return json_encode(array("code"=>false ));
        }
    }

    public function eliminarCorreos(Request $request){
        $correos = correos::find($request->id);
        if($correos->delete()){
            return json_encode(array("code"=>true ));
        }else{
            return json_encode(array("code"=>false ));
        }
    }

    

}   













