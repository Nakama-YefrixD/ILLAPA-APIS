<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Response;
use Auth;
use App\sectores;
use App\personas;
use App\User;
use App\clientes;
use App\correos;
use App\telefonos;
use App\direcciones;
use App\documentos;
use App\empresas;
use App\tiposDocumentosIdentidad;
use App\socios;
use App\tiposTelefonos;
use App\tiposMonedas;
use App\tiposDocumentos;
use App\tiposPagos;
use App\pagos;


class importarController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function ejemplosImportar()
    {

        return view('importar.ejemplos');
    }

    public function ejemplosimportarmostrar($nombreExcel)
    {   
        
        
        $archivo = "/ExamplesImportacion/D".$nombreExcel.".xlsx";
        $objPHPExcel = IOFactory::load($archivo);
        $objPHPExcel->setActiveSheetIndex(0);
        $numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
        $ultimaColumna = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
        
        

        $tabla = '<table class="table table-striped table-bordered table-hover'; 
        $tabla .= 'display nowrap mb-0" cellspacing="0" width="100%"><tbody>';

        for ($i=1; $i <=$numRows ; $i++) {
            $tabla .= '<tr>';
            foreach(range('A', $ultimaColumna) as $abc) {  
                $columnasFilas = $objPHPExcel->getActiveSheet()->getCell($abc.$i)->getCalculatedValue();
                if($i == 1){
                    $tabla .= '<th>'.$columnasFilas.'</th>';
                }else{
                    $tabla .= '<td>'.$columnasFilas.'</td>';
                }
                
            }

            $tabla .=  '<tr>';
        }

        $tabla .= '</tbody></table>';

        
        $output = array(
            'estado'     =>  'correcto',
            'mensaje'   => $tabla
        );

        
        echo json_encode($output);
            

    }

    public function ejemplosimportardescargar($nombreExcel)
    {
        $file= public_path(). "/ExamplesImportacion/D".$nombreExcel.".xlsx";
        $headers = array(
            'Content-Type: application/xlsx',
            );

        return Response::download($file, $nombreExcel.'.xlsx', $headers);
    }

    public function datos()
    {

        return view('importar.datos');
    }

    public function mostrarExcel(Request $request)
    {

        $archivo = $_FILES['excel']['name'];

        $objPHPExcel = IOFactory::load($_FILES['excel']['tmp_name']);
        $objPHPExcel->setActiveSheetIndex(0);
        $numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
        $ultimaColumna = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
        
        

        $tabla = '<table class="table table-striped table-bordered table-hover'; 
        $tabla .= 'display nowrap mb-0" cellspacing="0" width="100%"><tbody>';

        for ($i=1; $i <=$numRows ; $i++) {
            $tabla .= '<tr>';
            foreach(range('A', $ultimaColumna) as $abc) {  
                $columnasFilas = $objPHPExcel->getActiveSheet()->getCell($abc.$i)->getCalculatedValue();
                if($i == 1){
                    $tabla .= '<th>'.$columnasFilas.'</th>';
                }else{
                    $tabla .= '<td>'.$columnasFilas.'</td>';
                }
                
            }

            $tabla .=  '<tr>';
        }

        $tabla .= '</tbody></table>';

        $output = array(
            'estado'     =>  'correcto',
            'mensaje'   => $tabla
        );
        

        echo json_encode($output);
    }

    public function importarClientes(Request $request)
    {
            $destino = "SubirExcels/bak_Clientes.xlsx" ;

            $objPHPExcel = IOFactory::load($_FILES['excel']['tmp_name']);
            $objPHPExcel->setActiveSheetIndex(0);
            $numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
            $ultimaColumna = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
            $userId = Auth::id();
            $socio = socios::where('correo_id','=',$userId)
                                ->first();
            
            for ($i=2; $i <= $numRows ; $i++) {
                
                $tipoIdentificacion = $objPHPExcel->getActiveSheet()->getCell('a'.$i)->getCalculatedValue();
                $numeroIdentificacion = $objPHPExcel->getActiveSheet()->getCell('b'.$i)->getCalculatedValue();
                $nombre = $objPHPExcel->getActiveSheet()->getCell('c'.$i)->getCalculatedValue();
                $sectorNombre = $objPHPExcel->getActiveSheet()->getCell('d'.$i)->getCalculatedValue();
                

                $tiposDocumentosIdentidad = tiposDocumentosIdentidad::where('nombre','=',$tipoIdentificacion)
                                                                    ->first();
                
                if(!$tiposDocumentosIdentidad){
                    break;
                }

                

                $sectorId = null;
                
                $sector = sectores::where('socio_id','=', $socio->id)
                                    ->where('descripcion','=', $sectorNombre)
                                    ->first();
                if($sector){
                    $sectorId = $sector->id; 
                    
                }else{
                    $sectorNuevo = new sectores;
                    $sectorNuevo->socio_id      = $socio->id;
                    $sectorNuevo->sectorista_id = null;
                    $sectorNuevo->descripcion   = $sectorNombre;
                    $sectorNuevo->estado        = 0;
                    $sectorNuevo->estSectorista = 0;
                    $sectorNuevo->estGestor     = 0;
                    $sectorNuevo->save();
                }
                
                
                if($sectorId == null){
                    break;
                }
                

                $siPersona = personas::where('numeroidentificacion', '=', $numeroIdentificacion)
                                    ->first();



                if($siPersona){

                    $personaId = $siPersona->id;
                    $exisUser = User::where('persona_id', '=', $personaId )
                                                    ->first();
                    if($exisUser){
                        $userId = $exisUser->id;
                        $exisCliente= clientes::where('correo_id', '=', $userId )
                                                ->where('sector_id','=',$sectorId)
                                                ->first();
                        if($exisCliente){

                        }else{
                            $cliente = new clientes;
                            $cliente->sector_id = $sectorId;
                            $cliente->correo_id = $userId;
                            $cliente->estado = 1;
                            $cliente->save();
                            $idCliente = $cliente->id;
                        }



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

                        $cliente = new clientes;
                        $cliente->sector_id = $sectorId;
                        $cliente->correo_id = $userId;
                        $cliente->estado = 1;
                        $cliente->save();
                        $idCliente = $cliente->id;
                    }

                }else{

                    $personas = new personas;
                    $personas->tipoDocumentoIdentidad_id = $tiposDocumentosIdentidad->id;
                    $personas->numeroidentificacion = $numeroIdentificacion;
                    $personas->nombre = $nombre;
                    $personas->imagen = 'imagenes_clientes/clientes.png';
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

                    $cliente = new clientes;
                    $cliente->sector_id = $sectorId;
                    $cliente->correo_id = $userId;
                    $cliente->estado = 1;
                    $cliente->save();
                    $idCliente = $cliente->id;

                }
            }
        
        $output = array(
            'estado'     =>  'correcto',
            'mensaje'   => 'bien'
        );

        echo json_encode($output);


    }

    public function importarCorreos(Request $request)
    {

        $archivo = "ExamplesImportacion/Dcorreos.xlsx";
        $objPHPExcel = IOFactory::load($_FILES['excel']['tmp_name']);
        $objPHPExcel->setActiveSheetIndex(0);
        $numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
        $ultimaColumna = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
        $userId = Auth::id();
        $socio = socios::where('correo_id','=',$userId)
                                ->first();
        
        

        for ($i=2; $i <= $numRows ; $i++) {
            $tipoIdentificacion = $objPHPExcel->getActiveSheet()->getCell('a'.$i)->getCalculatedValue();
            $numeroIdentificacion = $objPHPExcel->getActiveSheet()->getCell('b'.$i)->getCalculatedValue();
            $correo = $objPHPExcel->getActiveSheet()->getCell('c'.$i)->getCalculatedValue();
            
            $tiposDocumentosIdentidad = tiposDocumentosIdentidad::where('nombre','=',$tipoIdentificacion)
                                                                    ->first();
                
            if(!$tiposDocumentosIdentidad){
                break;
            }

            $siPersona = personas::where('numeroidentificacion', '=', $numeroIdentificacion)
                                    ->where('tipoDocumentoIdentidad_id', '=', $tiposDocumentosIdentidad->id)
                                    ->first();
            
            if($siPersona){
                $personaId = $siPersona->id;
                $exisUser = User::where('persona_id', '=', $personaId )
                                ->first();
                if($exisUser){
                    $userId = $exisUser->id;

                    $sectores = sectores::where('socio_id','=',$socio->id)
                                            ->get();

                    foreach($sectores as $sector){
                        $exisCliente= clientes::where('correo_id', '=', $userId )
                                            ->where('sector_id','=',$sector->id)
                                            ->first();
                        if($exisCliente){
                            $correos = new correos;
                            $correos->cliente_id = $exisCliente->id;
                            $correos->correo_id = $userId;
                            $correos->correo = $correo;
                            $correos->estado = 1;
                            $correos->save();

                        }else{
                            
                        }
                    }
                    

                    



                }else{

                }
            }else{

            }

        }

        $output = array(
            'estado'     =>  'correcto',
            'mensaje'   => 'bien'
        );
    
        echo json_encode($output);

    }



    public function importarTelefonos(Request $request)
    {

        $archivo = "ExamplesImportacion/Dtelefonos.xlsx";
        $objPHPExcel = IOFactory::load($_FILES['excel']['tmp_name']);
        $objPHPExcel->setActiveSheetIndex(0);
        $numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
        $ultimaColumna = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
        $userId = Auth::id();
        $socio = socios::where('correo_id','=',$userId)
                        ->first();
        

        for ($i=2; $i <= $numRows ; $i++) {
            
            $tipoIdentificacion = $objPHPExcel->getActiveSheet()->getCell('a'.$i)->getCalculatedValue();
            $numeroIdentificacion = $objPHPExcel->getActiveSheet()->getCell('b'.$i)->getCalculatedValue();
            $tipoTelefonoExcel = $objPHPExcel->getActiveSheet()->getCell('c'.$i)->getCalculatedValue();
            $pais = $objPHPExcel->getActiveSheet()->getCell('d'.$i)->getCalculatedValue();
            $prefijo = $objPHPExcel->getActiveSheet()->getCell('e'.$i)->getCalculatedValue();
            $numero = $objPHPExcel->getActiveSheet()->getCell('f'.$i)->getCalculatedValue();

            $tiposDocumentosIdentidad = tiposDocumentosIdentidad::where('nombre','=',$tipoIdentificacion)
                                                                    ->first();
                
            if(!$tiposDocumentosIdentidad){
                break;
            }
            $tipoTelefono = tiposTelefonos::where('nombre','=',$tipoTelefonoExcel)
                                            ->first();
            if(!$tipoTelefono){
                break;
            }

            $siPersona = personas::where('numeroidentificacion', '=', $numeroIdentificacion)
                                    ->first();

            if($siPersona){
                $personaId = $siPersona->id;
                $exisUser = User::where('persona_id', '=', $personaId )
                                                ->first();
                if($exisUser){
                    $userId = $exisUser->id;
                    
                    $sectores = sectores::where('socio_id','=',$socio->id)
                                            ->get();
                    
                    foreach($sectores as $sector){
                        $exisCliente= clientes::where('correo_id', '=', $userId )
                                            ->where('sector_id','=',$sector->id)
                                            ->first();
                        if($exisCliente){
                            $telefonos = new telefonos;
                            $telefonos->cliente_id = $exisCliente->id;
                            $telefonos->correo_id = $userId;
                            $telefonos->pais = $pais;
                            $telefonos->prefijo = $prefijo;
                            $telefonos->numero = $numero;
                            $telefonos->tipotelefono_id = $tipoTelefono->id;
                            $telefonos->estado = 1;
                            $telefonos->save();

                        }else{
                            
                        }
                    }
                    

                }else{

                }
            }else{

            }
        }

        $output = array(
            'estado'     =>  'correcto',
            'mensaje'   => 'bien'
        );
    
        echo json_encode($output);


    }

    public function importarDirecciones(Request $request)
    {

        $archivo = "ExamplesImportacion/Ddirecciones.xlsx";
        $objPHPExcel = IOFactory::load($_FILES['excel']['tmp_name']);
        $objPHPExcel->setActiveSheetIndex(0);
        $numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
        $ultimaColumna = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
        $userId = Auth::id();

        $socio = socios::where('correo_id','=',$userId)
                        ->first();

        for ($i=2; $i <= $numRows ; $i++) {
            
            $tipoIdentificacion = $objPHPExcel->getActiveSheet()->getCell('a'.$i)->getCalculatedValue();
            $numeroIdentificacion = $objPHPExcel->getActiveSheet()->getCell('b'.$i)->getCalculatedValue();
            $direccion = $objPHPExcel->getActiveSheet()->getCell('c'.$i)->getCalculatedValue();
            $ciudad = $objPHPExcel->getActiveSheet()->getCell('d'.$i)->getCalculatedValue();
            $codigoPostal = $objPHPExcel->getActiveSheet()->getCell('e'.$i)->getCalculatedValue();
            $pais = $objPHPExcel->getActiveSheet()->getCell('f'.$i)->getCalculatedValue();
            $latitud = $objPHPExcel->getActiveSheet()->getCell('g'.$i)->getCalculatedValue();
            $longitud = $objPHPExcel->getActiveSheet()->getCell('h'.$i)->getCalculatedValue();

            $tiposDocumentosIdentidad = tiposDocumentosIdentidad::where('nombre','=',$tipoIdentificacion)
                                                                    ->first();
                
            if(!$tiposDocumentosIdentidad){
                break;
            }

            
            $siPersona = personas::where('numeroidentificacion', '=', $numeroIdentificacion)
                                    ->first();

            if($siPersona){
                $personaId = $siPersona->id;
                $exisUser = User::where('persona_id', '=', $personaId )
                                ->first();
                if($exisUser){
                    $userId = $exisUser->id;
                    
                    $sectores = sectores::where('socio_id','=',$socio->id)
                                            ->get();
                    
                    foreach($sectores as $sector){
                        $exisCliente= clientes::where('correo_id', '=', $userId )
                                            ->where('sector_id','=',$sector->id)
                                            ->first();
                        if($exisCliente){
                            $direcciones = new direcciones;
                            $direcciones->cliente_id = $exisCliente->id;
                            $direcciones->correo_id = $userId;
                            $direcciones->calle = $direccion;
                            $direcciones->ciudad = $ciudad;
                            $direcciones->codigopostal = $codigoPostal;
                            $direcciones->pais = $pais;
                            $direcciones->latitud = $latitud;
                            $direcciones->longitud = $longitud;

                            $direcciones->estado = 1;
                            $direcciones->save();

                        }else{
                            
                        }
                    }
                    
                }else{
                }
            }else{
            }
        }

        $output = array(
            'estado'     =>  'correcto',
            'mensaje'   => 'bien'
        );
    
        echo json_encode($output);

    }

    public function importarDocumentos(Request $request)
    {

        $archivo = "ExamplesImportacion/Ddocumentos.xlsx";
        $objPHPExcel = IOFactory::load($_FILES['excel']['tmp_name']);
        $objPHPExcel->setActiveSheetIndex(0);
        $numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
        $ultimaColumna = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
        $userId = Auth::id();

        $socio = socios::where('correo_id','=',$userId)
                        ->first();

        for ($i=2; $i <= $numRows ; $i++) {
            
            $tipoIdentificacion = $objPHPExcel->getActiveSheet()->getCell('a'.$i)->getCalculatedValue();
            $numeroIdentificacion = $objPHPExcel->getActiveSheet()->getCell('b'.$i)->getCalculatedValue();
            $tipoDocumento = $objPHPExcel->getActiveSheet()->getCell('c'.$i)->getCalculatedValue();
            $numeroDocumento = $objPHPExcel->getActiveSheet()->getCell('d'.$i)->getCalculatedValue();
            $fechaEmision = $objPHPExcel->getActiveSheet()->getCell('e'.$i)->getCalculatedValue();
            $fechaVencida = $objPHPExcel->getActiveSheet()->getCell('f'.$i)->getCalculatedValue();
            $moneda = $objPHPExcel->getActiveSheet()->getCell('g'.$i)->getCalculatedValue();
            $importe = $objPHPExcel->getActiveSheet()->getCell('h'.$i)->getCalculatedValue();

            $tiposDocumentosIdentidad = tiposDocumentosIdentidad::where('nombre','=',$tipoIdentificacion)
                                                                    ->first();
                
            if(!$tiposDocumentosIdentidad){
                break;
            }

            $tiposMonedas = tiposMonedas::where('nombre','=',$moneda)
                                        ->first();
                
            if(!$tiposMonedas){
                break;
            }

            $tiposDocumentos = tiposDocumentos::where('nombre','=',$tipoDocumento)
                                                ->first();
                
            if(!$tiposDocumentos){
                break;
            }
            


            $siPersona = personas::where('numeroidentificacion', '=', $numeroIdentificacion)
                                    ->first();

            if($siPersona){
                $personaId = $siPersona->id;
                $exisUser = User::where('persona_id', '=', $personaId )
                                                ->first();
                if($exisUser){
                    $userId = $exisUser->id;

                    $sectores = sectores::where('socio_id','=',$socio->id)
                                            ->get();
                    
                    foreach($sectores as $sector){
                        $exisCliente= clientes::where('correo_id', '=', $userId )
                                            ->where('sector_id','=',$sector->id)
                                            ->first();


                        if($exisCliente){
                            $exisDocumento = documentos::where('numero', '=', $numeroDocumento )
                                                        ->where('tipoDocumento_id', '=', $tiposDocumentos->id)
                                                        ->where('cliente_id', '=', $exisCliente->id)
                                                        ->first(); 
                            if($exisDocumento){
                                $documento = documentos::find($exisDocumento->id);
                                $documento->importe = $exisDocumento->importe;
                                $documento->saldo = $exisDocumento->importe;
                                $documento->update();
                            }else{
                                $documento = new documentos;
                                $documento->cliente_id = $exisCliente->id;
                                $documento->tipoDocumento_id  = $tiposDocumentos->id;
                                $documento->numero = $numeroDocumento;
                                $documento->fechaemision = $fechaEmision;
                                $documento->fechavencimiento = $fechaVencida;
                                $documento->tipoMoneda_id = $tiposMonedas->id;
                                $documento->importe = $importe;
                                $documento->saldo = $importe;
                                $documento->estado = 1;
                                $documento->save();
                            }                       
                            

                        }else{
                            
                        }
                    }
                    
                    

                }else{
                    echo "No existe este Usuario";
                }
            }else{
                echo "No existe esta persona";
            }

        }

        $output = array(
            'estado'     =>  'correcto',
            'mensaje'   => 'bien'
        );
    
        echo json_encode($output);

    }

    public function importarPagos(Request $request)
    {

        $archivo = "ExamplesImportacion/Dpagos.xlsx";
        $objPHPExcel = IOFactory::load($_FILES['excel']['tmp_name']);
        $objPHPExcel->setActiveSheetIndex(0);
        $numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
        $ultimaColumna = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
        $userId = Auth::id();
        $socio = socios::where('correo_id','=',$userId)
                        ->first();

           

        for ($i=2; $i <= $numRows ; $i++) {
            
            $tipoIdentificacion = $objPHPExcel->getActiveSheet()->getCell('a'.$i)->getCalculatedValue();
            $numeroIdentificacion = $objPHPExcel->getActiveSheet()->getCell('b'.$i)->getCalculatedValue();
            $tipoDocumento = $objPHPExcel->getActiveSheet()->getCell('c'.$i)->getCalculatedValue();
            $numeroDocumento = $objPHPExcel->getActiveSheet()->getCell('d'.$i)->getCalculatedValue();
            $tipoPago = $objPHPExcel->getActiveSheet()->getCell('e'.$i)->getCalculatedValue();
            $numeroPago = $objPHPExcel->getActiveSheet()->getCell('f'.$i)->getCalculatedValue();
            $fechaEmision = $objPHPExcel->getActiveSheet()->getCell('g'.$i)->getCalculatedValue();
            $importe = $objPHPExcel->getActiveSheet()->getCell('h'.$i)->getCalculatedValue();

            $tiposDocumentosIdentidad = tiposDocumentosIdentidad::where('nombre','=',$tipoIdentificacion)
                                                                 ->first();
                
            if(!$tiposDocumentosIdentidad){
                break;
            }
            
            $tiposPagos = tiposPagos::where('nombre','=',$tipoPago)
                                    ->first();
                
            if(!$tiposPagos){
                break;
            }

            $tiposDocumentos = tiposDocumentos::where('nombre','=',$tipoDocumento)
                                                ->first();
                
            if(!$tiposDocumentos){
                break;
            }

            $siPersona = personas::where('numeroidentificacion', '=', $numeroIdentificacion)
                                    ->first();

            if($siPersona){
                $personaId = $siPersona->id;
                $exisUser = User::where('persona_id', '=', $personaId )
                                    ->first();
                if($exisUser){
                    $userId = $exisUser->id;

                    $sectores = sectores::where('socio_id','=',$socio->id)
                                            ->get();
                    
                    foreach($sectores as $sector){
                        $exisCliente= clientes::where('correo_id', '=', $userId )
                                            ->where('sector_id','=',$sector->id)
                                            ->first();
                                            
                        

                        if($exisCliente){
                            $documentos = documentos::select('documentos.id as id', 
                                                            'documentos.saldo as saldo', 
                                                            'documentos.importe as importe', 
                                                            'tm.nombre as monedaNombre')
                                                    ->where('documentos.cliente_id','=',$exisCliente->id)
                                                    ->where('documentos.numero','=',$numeroDocumento)
                                                    ->where('documentos.tipoDocumento_id','=', $tiposDocumentos->id )
                                                    ->join('tiposMonedas as tm', 'tm.id','=','documentos.tipoMoneda_id')
                                                    ->first();
                            if($documentos){
                                $saldo = $documentos->saldo - $importe;
                                
                                $documento = documentos::find($documentos->id);
                                $documento->saldo = $saldo;
                                $documento->update();

                                $pago = new pagos;
                                $pago->documento_id = $documentos->id;
                                $pago->tipoPago_id = $tiposPagos->id;
                                $pago->numero = $numeroPago;
                                $pago->fechaemision = $fechaEmision;
                                $pago->fechavencimiento = $fechaEmision;
                                $pago->moneda = $documentos->monedaNombre;
                                $pago->importe = $importe;
                                $pago->saldo = $saldo;
                                $pago->estado = 1;
                                $pago->save();
                            }

                        }else{
                            
                        }
                    }

                }else{
                    echo "No existe este Usuario";
                }
            }else{
                echo "No existe esta persona";
            }



        }

        $output = array(
            'estado'     =>  'correcto',
            'mensaje'   => 'bien'
        );
    
        echo json_encode($output);

    }

}
