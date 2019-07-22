<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class importarFree extends Controller
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
        
        
        $archivo = "ExamplesImportacion/D".$nombreExcel.".xlsx";
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
        $file= public_path(). "\ExamplesImportacion\D".$nombreExcel.".xlsx";
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
            $sectorId = sectores::select('sectores.id as sectorId')
                                ->join('sectoristas as scts','scts.id', '=','sectores.sectorista_id')
                                ->join('users as u', 'u.id', '=', 'scts.correo_id')
                                ->where('u.id', '=',$userId )
                                ->where('sectores.estado', '=', 1 )
                                ->first();
            

            for ($i=2; $i <= $numRows ; $i++) {
                
                $tipoIdentificacion = $objPHPExcel->getActiveSheet()->getCell('a'.$i)->getCalculatedValue();
                $numeroIdentificacion = $objPHPExcel->getActiveSheet()->getCell('b'.$i)->getCalculatedValue();
                $nombre = $objPHPExcel->getActiveSheet()->getCell('c'.$i)->getCalculatedValue();
                
                if($tipoIdentificacion != "DNI" && $tipoIdentificacion != "RUC"){
                    break;
                }
            


                if(strlen($tipoIdentificacion) == "DNI"  ){
                    $identificadorRucDni = 1;
        
                }else{
        
                    $identificadorRucDni = 2;
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
                                                    ->first();
                        if($exisCliente){

                        }else{
                            $cliente = new clientes;
                            $cliente->sector_id = $sectorId->sectorId;
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
                        $cliente->sector_id = $sectorId->sectorId;
                        $cliente->correo_id = $userId;
                        $cliente->estado = 1;
                        $cliente->save();
                        $idCliente = $cliente->id;
                    }

                }else{

                    $personas = new personas;
                    $personas->tipoidentificacion = $identificadorRucDni;
                    $personas->numeroidentificacion = $numeroIdentificacion;
                    $personas->nombre = $nombre;
                    $personas->imagen = 'https://cdn.pixabay.com/photo/2016/06/03/15/35/customer-service-1433640_960_720.png';
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
                    $cliente->sector_id = $sectorId->sectorId;
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

        $sectorId = sectores::select('sectores.id as sectorId')
                            ->join('sectoristas as scts','scts.id', '=','sectores.sectorista_id')
                            ->join('users as u', 'u.id', '=', 'scts.correo_id')
                            ->where('u.id', '=',$userId )
                            ->where('sectores.estado', '=', 1 )
                            ->first();

        for ($i=2; $i <= $numRows ; $i++) {
            $tipoIdentificacion = $objPHPExcel->getActiveSheet()->getCell('a'.$i)->getCalculatedValue();
            $numeroIdentificacion = $objPHPExcel->getActiveSheet()->getCell('b'.$i)->getCalculatedValue();
            $correo = $objPHPExcel->getActiveSheet()->getCell('c'.$i)->getCalculatedValue();

            if($tipoIdentificacion != "DNI" && $tipoIdentificacion != "RUC"){
                break;
            }

            if($tipoIdentificacion == "DNI"  ){
                $identificadorRucDni = 1;
    
            }else{
    
                $identificadorRucDni = 2;
            }

            $siPersona = personas::where('numeroidentificacion', '=', $numeroIdentificacion)
                                // ->where('tipoidentificacion', '=', $identificadorRucDni )
                                ->first();
            
            if($siPersona){
                $personaId = $siPersona->id;
                $exisUser = User::where('persona_id', '=', $personaId )
                                                ->first();
                if($exisUser){
                    $userId = $exisUser->id;
                    $exisCliente= clientes::where('correo_id', '=', $userId )
                                            ->where('sector_id', '=', $sectorId->sectorId)
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



                }else{

                }
            }else{

            }

        }

    }

    public function importarTelefonos(Request $request)
    {

        $archivo = "ExamplesImportacion/Dtelefonos.xlsx";
        $objPHPExcel = IOFactory::load($_FILES['excel']['tmp_name']);
        $objPHPExcel->setActiveSheetIndex(0);
        $numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
        $ultimaColumna = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
        $userId = Auth::id();

        $sectorId = sectores::select('sectores.id as sectorId')
                            ->join('sectoristas as scts','scts.id', '=','sectores.sectorista_id')
                            ->join('users as u', 'u.id', '=', 'scts.correo_id')
                            ->where('u.id', '=',$userId )
                            ->where('sectores.estado', '=', 1 )
                            ->first();

        for ($i=2; $i <= $numRows ; $i++) {
            
            $tipoIdentificacion = $objPHPExcel->getActiveSheet()->getCell('a'.$i)->getCalculatedValue();
            $numeroIdentificacion = $objPHPExcel->getActiveSheet()->getCell('b'.$i)->getCalculatedValue();
            $tipoTelefonoExcel = $objPHPExcel->getActiveSheet()->getCell('c'.$i)->getCalculatedValue();
            $prefijo = $objPHPExcel->getActiveSheet()->getCell('d'.$i)->getCalculatedValue();
            $numero = $objPHPExcel->getActiveSheet()->getCell('e'.$i)->getCalculatedValue();

            if($tipoIdentificacion != "DNI" && $tipoIdentificacion != "RUC"){
                break;
            }

            if($tipoTelefonoExcel == "CASA"){
                $tipoTelefono = 1;
            }else if($tipoTelefonoExcel == "TRABAJO"){
                $tipoTelefono = 2;
            }else if($tipoTelefonoExcel == "PERSONAL"){
                $tipoTelefono = 3;
            }else if($tipoTelefonoExcel = "EMPRESA"){
                $tipoTelefono = 4;
            }else{
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
                                            ->where('sector_id', '=', $sectorId->sectorId)
                                            ->first();
                    if($exisCliente){

                        
                        $telefonos = new telefonos;
                        $telefonos->cliente_id = $exisCliente->id;
                        $telefonos->correo_id = $userId;
                        $telefonos->prefijo = $prefijo;
                        $telefonos->numero = $numero;
                        $telefonos->tipo = $tipoTelefono;
                        $telefonos->estado = 1;
                        $telefonos->save();

                    }else{
                        
                    }



                }else{

                }
            }else{

            }



        }

    }

    public function importarDirecciones(Request $request)
    {

        $archivo = "ExamplesImportacion/Ddirecciones.xlsx";
        $objPHPExcel = IOFactory::load($_FILES['excel']['tmp_name']);
        $objPHPExcel->setActiveSheetIndex(0);
        $numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
        $ultimaColumna = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
        $userId = Auth::id();

        $sectorId = sectores::select('sectores.id as sectorId')
                            ->join('sectoristas as scts','scts.id', '=','sectores.sectorista_id')
                            ->join('users as u', 'u.id', '=', 'scts.correo_id')
                            ->where('u.id', '=',$userId )
                            ->where('sectores.estado', '=', 1 )
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

            if($tipoIdentificacion != "DNI" && $tipoIdentificacion != "RUC"){
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
                                            ->where('sector_id', '=', $sectorId->sectorId)
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



                }else{

                }
            }else{

            }



        }

    }

    public function importarDocumentos(Request $request)
    {

        $archivo = "ExamplesImportacion/Ddocumentos.xlsx";
        $objPHPExcel = IOFactory::load($_FILES['excel']['tmp_name']);
        $objPHPExcel->setActiveSheetIndex(0);
        $numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
        $ultimaColumna = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
        $userId = Auth::id();

        $sectorId = sectores::select('sectores.id as sectorId')
                            ->join('sectoristas as scts','scts.id', '=','sectores.sectorista_id')
                            ->join('users as u', 'u.id', '=', 'scts.correo_id')
                            ->where('u.id', '=',$userId )
                            ->where('sectores.estado', '=', 1 )
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

            if($tipoIdentificacion != "DNI" && $tipoIdentificacion != "RUC"){
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
                                            ->where('sector_id', '=', $sectorId->sectorId)
                                            ->first();
                    if($exisCliente){

                        
                        $documento = new documentos;
                        $documento->cliente_id = $exisCliente->id;
                        $documento->tipo = $tipoDocumento;
                        $documento->numero = $numeroDocumento;
                        $documento->fechaemision = $fechaEmision;
                        $documento->fechavencimiento = $fechaVencida;
                        $documento->moneda = $moneda;
                        $documento->importe = $importe;
                        $documento->saldo = $importe;
                        $documento->estado = 1;
                        $documento->save();

                    }else{
                        echo "No existe este cliente";
                    }



                }else{
                    echo "No existe este Usuario";
                }
            }else{
                echo "No existe esta persona";
            }



        }

    }

    public function importarPagos(Request $request)
    {

        $archivo = "ExamplesImportacion/Dpagos.xlsx";
        $objPHPExcel = IOFactory::load($_FILES['excel']['tmp_name']);
        $objPHPExcel->setActiveSheetIndex(0);
        $numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
        $ultimaColumna = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
        $userId = Auth::id();

        $sectorId = sectores::select('sectores.id as sectorId')
                            ->join('sectoristas as scts','scts.id', '=','sectores.sectorista_id')
                            ->join('users as u', 'u.id', '=', 'scts.correo_id')
                            ->where('u.id', '=',$userId )
                            ->where('sectores.estado', '=', 1 )
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

            if($tipoIdentificacion != "DNI" && $tipoIdentificacion != "RUC"){
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
                                            ->where('sector_id', '=', $sectorId->sectorId)
                                            ->first();
                    if($exisCliente){

                       $documentos = documentos::where('cliente_id', '=', $exisCliente->id)
                                                    ->where('numero','=',$numeroDocumento)
                                                    ->first();
                        if($documentos){
                            $saldo = $documentos->importe - $importe;
                            $pago = new pagos;
                            $pago->documento_id = $documentos->id;
                            $pago->tipo = $tipoPago;
                            $pago->numero = $numeroPago;
                            $pago->fechaemision = $fechaEmision;
                            $pago->fechavencimiento = $fechaEmision;
                            $pago->moneda = $documentos->moneda;
                            $pago->importe = $importe;
                            $pago->saldo = $saldo;
                            $pago->estado = 1;
                            $pago->save();

                        }else{
                            echo "No existe el documento";
                        }

                    }else{
                        echo "No existe este cliente";
                    }



                }else{
                    echo "No existe este Usuario";
                }
            }else{
                echo "No existe esta persona";
            }



        }

    }

}
