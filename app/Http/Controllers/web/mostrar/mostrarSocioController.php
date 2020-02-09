<?php

namespace App\Http\Controllers\web\mostrar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\socios;
use App\tiposDocumentosIdentidad;
use App\sectores;
use App\personas;
use App\tiposTelefonos;
use App\tiposDocumentos;
use App\tiposMonedas;
use App\User;
use App\clientes;
use App\tiposPagos;
use App\pagos;

use App\correos;
use App\telefonos;
use App\direcciones;
use App\documentos;
use App\empresas;

use Peru\Jne\Dni;
use Peru\Jne\DniParser;
use Peru\Sunat\Ruc;
use Peru\Sunat\RucParser;
use Peru\Sunat\HtmlParser;
use Peru\Http\ContextClient;

use Auth;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class mostrarSocioController extends Controller
{
    public function clientes(Request $request)
    {
        $objPHPExcel = IOFactory::load($_FILES['excel']['tmp_name']);
        $objPHPExcel->setActiveSheetIndex(0);
        $numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
        $ultimaColumna = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
        $userId = Auth::id();
        $socio  = socios::where('correo_id','=',$userId)
                            ->first();
        
        $tabla = '<tr>';
        $tabla .= '<th>TIPO IDENTIFICACIÓN</th>';
        $tabla .= '<th>NUMERO DE IDENTIFICACIÓN</th>';
        $tabla .= '<th>NOMBRE</th>';
        $tabla .= '<th>SECTOR</th>';
        $tabla .= '</tr>';

        for ($i=2; $i <= $numRows; $i++) {
            $tabla .= '<tr>';

            $tipoIdentificacion     = $objPHPExcel->getActiveSheet()->getCell('a'.$i)->getCalculatedValue();
            $numeroIdentificacion   = $objPHPExcel->getActiveSheet()->getCell('b'.$i)->getCalculatedValue();
            $nombre                 = $objPHPExcel->getActiveSheet()->getCell('c'.$i)->getCalculatedValue();
            $sectorNombre           = $objPHPExcel->getActiveSheet()->getCell('d'.$i)->getCalculatedValue();
            
            $tiposDocumentosIdentidad = tiposDocumentosIdentidad::where('nombre','=',$tipoIdentificacion)
                                                                ->first();
            
            if($tiposDocumentosIdentidad){
                $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$tipoIdentificacion.'</td>';
                
                if($tiposDocumentosIdentidad->id == 1){
                    if(strlen($numeroIdentificacion) == 8){
                        $cs = new Dni(new ContextClient(), new DniParser());
    
                        $person = $cs->get($numeroIdentificacion);
                        if ($person === null) {
                            $tabla .= '<td tipoError="ADVERTENCIA" error="Este numero de DNI no existe en el servidor de JNE del Perú" style="background:yellow;">'.$numeroIdentificacion.'</td>';
                            $tabla .= '<td tipoError="ADVERTENCIA" error="El numero de DNI no existe en el servidor de JNE del Perú" style="background:yellow;">'.$nombre.'</td>';
                            
                        }else{
                            $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$numeroIdentificacion.'</td>';
                            $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$person->nombres." ".$person->apellidoPaterno." ".$person->apellidoMaterno.'</td>';
                        }
    
                    }else{
                        $tabla .= '<td tipoError="ERROR" error="Este numero pertenece al tipo de identificacion DNI por ende debe contener 8 digitos" style="background:red;">'.$numeroIdentificacion.'</td>';
                        $tabla .= '<td tipoError="ERROR" error="El numero de DNI debe tener 8 digitos" style="background:red;">'.$nombre.'</td>';
                        
                    }
                }else if($tiposDocumentosIdentidad->id == 2){
                    if(strlen($numeroIdentificacion) == 11){
                        $cs = new Ruc(new ContextClient(), new RucParser(new HtmlParser()));
                        $company = $cs->get($numeroIdentificacion);
                        if ($company === null) {
                            $tabla .= '<td tipoError="ADVERTENCIA" error="Este numero de RUC no existe en el servidor de la SUNAT" style="background:yellow;">'.$numeroIdentificacion.'</td>';
                            $tabla .= '<td tipoError="ADVERTENCIA" error="El numero de RUC no existe en el servidor de la SUNAT" style="background:yellow;">'.$nombre.'</td>';
                            
                        }else{
                            $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$numeroIdentificacion.'</td>';
                            $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$company->razonSocial.'</td>';
                        }
                    }else{
                        $tabla .= '<td tipoError="ERROR" error="Este numero de identificación debe tener 11 digitos" style="background:red;">'.$numeroIdentificacion.'</td>';
                        $tabla .= '<td tipoError="ERROR" error="El numero de RUC debe tener 11 digitos" style="background:red;">'.$nombre.'</td>';
                    }
                }else{
                    $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$numeroIdentificacion.'</td>';
                    $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$nombre.'</td>';
                }
            }else{
                $tabla .= '<td tipoError="ERROR" error="Este tipo de identificación no existe o esta mal escrito" style="background:red;">'.$tipoIdentificacion.'</td>';
                $tabla .= '<td tipoError="ERROR" error="El tipo de identificación no existe o esta mal escrito" style="background:red;">'.$numeroIdentificacion.'</td>';
                $tabla .= '<td tipoError="ERROR" error="El tipo de identificación no existe o esta mal escrito" style="background:red;">'.$nombre.'</td>';
            }

            

            $sector = sectores::where('socio_id','=', $socio->id)
                                ->where('descripcion','=', $sectorNombre)
                                ->first();
            if($sector){
                $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$sectorNombre.'</td>';
            }else{
                $tabla .= '<td tipoError="ERROR" error="El sector no existe o esta mal escrito" style="background:red;">'.$sectorNombre.'</td>';
            }

            $tabla .= '</tr>';
        }
        
        $output = array(
            'estado'     =>  'correcto',
            'mensaje'   => $tabla
        );

        echo json_encode($output);
    }

    public function correos(Request $request)
    {
        $objPHPExcel = IOFactory::load($_FILES['excel']['tmp_name']);
        $objPHPExcel->setActiveSheetIndex(0);
        $numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
        $ultimaColumna = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
        $userId = Auth::id();
        $socio  = socios::where('correo_id','=',$userId)
                            ->first();
        
        $tabla = '<tr>';
        $tabla .= '<th>TIPO IDENTIFICACIÓN</th>';
        $tabla .= '<th>NUMERO DE IDENTIFICACIÓN</th>';
        $tabla .= '<th>CORREO</th>';
        $tabla .= '</tr>';

        for ($i=2; $i <= $numRows; $i++) {
            $tabla .= '<tr>';

            $tipoIdentificacion     = $objPHPExcel->getActiveSheet()->getCell('a'.$i)->getCalculatedValue();
            $numeroIdentificacion   = $objPHPExcel->getActiveSheet()->getCell('b'.$i)->getCalculatedValue();
            $correo                 = $objPHPExcel->getActiveSheet()->getCell('c'.$i)->getCalculatedValue();
            
            $tiposDocumentosIdentidad = tiposDocumentosIdentidad::where('nombre','=',$tipoIdentificacion)
                                                                ->first();
            if($tiposDocumentosIdentidad){

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

                        for($x = 0; $x < sizeof($sectores); $x++ ){
                            
                            $exisCliente= clientes::where('correo_id', '=', $userId )
                                                ->where('sector_id','=',$sectores[$x]['id'])
                                                ->first();
                            if($exisCliente){
                                $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$tipoIdentificacion.'</td>';
                                $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$numeroIdentificacion.'</td>';
                                $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$correo.'</td>';
                                break;
                            }else if($x == sizeof($sectores)-1 ){
                                $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$tipoIdentificacion.'</td>';
                                $tabla .= '<td tipoError="ERROR" error="El cliente no existe o esta mal escrito" style="background:red;">'.$numeroIdentificacion.'</td>';
                                $tabla .= '<td tipoError="ERROR" error="El numero de identificación no existe o esta mal escrito" style="background:red;">'.$correo.'</td>';
                                break;
                            }
                        }
                    }else{
                        $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$tipoIdentificacion.'</td>';
                        $tabla .= '<td tipoError="ERROR" error="El cliente no existe o esta mal escrito" style="background:red;">'.$numeroIdentificacion.'</td>';
                        $tabla .= '<td tipoError="ERROR" error="El cliente no existe o esta mal escrito" style="background:red;">'.$correo.'</td>';
                    }
                }else{
                    $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$tipoIdentificacion.'</td>';
                    $tabla .= '<td tipoError="ERROR" error="El numero de identificación no existe o esta mal escrito" style="background:red;">'.$numeroIdentificacion.'</td>';
                    $tabla .= '<td tipoError="ERROR" error="El numero de identificación no existe o esta mal escrito" style="background:red;">'.$correo.'</td>';
                }

            }else{
                $tabla .= '<td tipoError="ERROR" error="Este tipo de identificación no existe o esta mal escrito" style="background:red;">'.$tipoIdentificacion.'</td>';
                $tabla .= '<td tipoError="ERROR" error="El tipo de identificación no existe o esta mal escrito" style="background:red;">'.$numeroIdentificacion.'</td>';
                $tabla .= '<td tipoError="ERROR" error="El tipo de identificación no existe o esta mal escrito" style="background:red;">'.$correo.'</td>';
            }

            $tabla .= '</tr>';

        }

        $output = array(
            'estado'     =>  'correcto',
            'mensaje'   => $tabla
        );

        echo json_encode($output);


    }

    public function telefonos(Request $request)
    {
        $objPHPExcel = IOFactory::load($_FILES['excel']['tmp_name']);
        $objPHPExcel->setActiveSheetIndex(0);
        $numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
        $ultimaColumna = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
        $userId = Auth::id();
        $socio  = socios::where('correo_id','=',$userId)
                            ->first();
        
        $tabla = '<tr>';
        $tabla .= '<th>TIPO IDENTIFICACIÓN</th>';
        $tabla .= '<th>NUMERO DE IDENTIFICACIÓN</th>';
        $tabla .= '<th>TIPO DE TELEFONO</th>';
        $tabla .= '<th>PAÍS</th>';
        $tabla .= '<th>PREFIJO</th>';
        $tabla .= '<th>NUMERO</th>';
        $tabla .= '</tr>';

        for ($i=2; $i <= $numRows; $i++) {
            $tabla .= '<tr>';

            $tipoIdentificacion     = $objPHPExcel->getActiveSheet()->getCell('a'.$i)->getCalculatedValue();
            $numeroIdentificacion   = $objPHPExcel->getActiveSheet()->getCell('b'.$i)->getCalculatedValue();
            $tipoTelefono           = $objPHPExcel->getActiveSheet()->getCell('c'.$i)->getCalculatedValue();
            $pais                   = $objPHPExcel->getActiveSheet()->getCell('d'.$i)->getCalculatedValue();
            $prefijo                = $objPHPExcel->getActiveSheet()->getCell('e'.$i)->getCalculatedValue();
            $numero                 = $objPHPExcel->getActiveSheet()->getCell('f'.$i)->getCalculatedValue();

            $tiposDocumentosIdentidad = tiposDocumentosIdentidad::where('nombre','=',$tipoIdentificacion)
                                                                ->first();
            if($tiposDocumentosIdentidad){

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

                        for($x = 0; $x < sizeof($sectores); $x++ ){
                            
                            $exisCliente= clientes::where('correo_id', '=', $userId )
                                                ->where('sector_id','=',$sectores[$x]['id'])
                                                ->first();
                            if($exisCliente){
                                $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$tipoIdentificacion.'</td>';
                                $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$numeroIdentificacion.'</td>';
                                break;
                            }else if($x == sizeof($sectores)-1 ){
                                $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$tipoIdentificacion.'</td>';
                                $tabla .= '<td tipoError="ERROR" error="El cliente no existe o esta mal escrito" style="background:red;">'.$numeroIdentificacion.'</td>';
                                break;
                            }
                        }
                    }else{
                        $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$tipoIdentificacion.'</td>';
                        $tabla .= '<td tipoError="ERROR" error="El cliente no existe o esta mal escrito" style="background:red;">'.$numeroIdentificacion.'</td>';
                    }
                }else{
                    $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$tipoIdentificacion.'</td>';
                    $tabla .= '<td tipoError="ERROR" error="El numero de identificación no existe o esta mal escrito" style="background:red;">'.$numeroIdentificacion.'</td>';
                }

            }else{
                $tabla .= '<td tipoError="ERROR" error="Este tipo de identificación no existe o esta mal escrito" style="background:red;">'.$tipoIdentificacion.'</td>';
                $tabla .= '<td tipoError="ERROR" error="El tipo de identificación no existe o esta mal escrito" style="background:red;">'.$numeroIdentificacion.'</td>';
            }

            $siTipoTelefono = tipostelefonos::where('nombre', $tipoTelefono)->first();
            if($siTipoTelefono){
                $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$tipoTelefono.'</td>';
            }else{  
                $tabla .= '<td tipoError="ERROR" error="El tipo de telefono no existe o esta mal escrito" style="background:red;">'.$tipoTelefono.'</td>';
            }
    
            $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$pais.'</td>';
    
            $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$prefijo.'</td>';
            
            if(is_numeric($numero)){
                $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$numero.'</td>';
            }else{
                $tabla .= '<td tipoError="ERROR" error="El tipo de telefono no existe o esta mal escrito" style="background:red;">'.$numero.'</td>';
            }
            
            $tabla .= '</tr>';

        }



        $output = array(
            'estado'     =>  'correcto',
            'mensaje'   => $tabla
        );

        echo json_encode($output);


    }

    public function direcciones(Request $request)
    {
        $objPHPExcel = IOFactory::load($_FILES['excel']['tmp_name']);
        $objPHPExcel->setActiveSheetIndex(0);
        $numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
        $ultimaColumna = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
        $userId = Auth::id();
        $socio  = socios::where('correo_id','=',$userId)
                            ->first();
        
        $tabla = '<tr>';
        $tabla .= '<th>TIPO IDENTIFICACIÓN</th>';
        $tabla .= '<th>NUMERO DE IDENTIFICACIÓN</th>';
        $tabla .= '<th>DIRECCIÓN</th>';
        $tabla .= '<th>CIUDAD</th>';
        $tabla .= '<th>CODIGO POSTAL</th>';
        $tabla .= '<th>PAÍS</th>';
        $tabla .= '<th>LATITUD</th>';
        $tabla .= '<th>LONGITUD</th>';
        $tabla .= '</tr>';

        for ($i=2; $i <= $numRows; $i++) {
            $tabla .= '<tr>';

            $tipoIdentificacion     = $objPHPExcel->getActiveSheet()->getCell('a'.$i)->getCalculatedValue();
            $numeroIdentificacion   = $objPHPExcel->getActiveSheet()->getCell('b'.$i)->getCalculatedValue();
            $direccion              = $objPHPExcel->getActiveSheet()->getCell('c'.$i)->getCalculatedValue();
            $ciudad                 = $objPHPExcel->getActiveSheet()->getCell('d'.$i)->getCalculatedValue();
            $codigoPostal           = $objPHPExcel->getActiveSheet()->getCell('e'.$i)->getCalculatedValue();
            $pais                   = $objPHPExcel->getActiveSheet()->getCell('f'.$i)->getCalculatedValue();
            $latitud                = $objPHPExcel->getActiveSheet()->getCell('g'.$i)->getCalculatedValue();
            $longitud               = $objPHPExcel->getActiveSheet()->getCell('h'.$i)->getCalculatedValue();


            $tiposDocumentosIdentidad = tiposDocumentosIdentidad::where('nombre','=',$tipoIdentificacion)
                                                                ->first();
            if($tiposDocumentosIdentidad){

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

                        for($x = 0; $x < sizeof($sectores); $x++ ){
                            
                            $exisCliente= clientes::where('correo_id', '=', $userId )
                                                ->where('sector_id','=',$sectores[$x]['id'])
                                                ->first();
                            if($exisCliente){
                                $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$tipoIdentificacion.'</td>';
                                $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$numeroIdentificacion.'</td>';
                                break;
                            }else if($x == sizeof($sectores)-1 ){
                                $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$tipoIdentificacion.'</td>';
                                $tabla .= '<td tipoError="ERROR" error="El cliente no existe o esta mal escrito" style="background:red;">'.$numeroIdentificacion.'</td>';
                                break;
                            }
                        }
                    }else{
                        $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$tipoIdentificacion.'</td>';
                        $tabla .= '<td tipoError="ERROR" error="El cliente no existe o esta mal escrito" style="background:red;">'.$numeroIdentificacion.'</td>';
                    }
                }else{
                    $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$tipoIdentificacion.'</td>';
                    $tabla .= '<td tipoError="ERROR" error="El numero de identificación no existe o esta mal escrito" style="background:red;">'.$numeroIdentificacion.'</td>';
                }

            }else{
                $tabla .= '<td tipoError="ERROR" error="Este tipo de identificación no existe o esta mal escrito" style="background:red;">'.$tipoIdentificacion.'</td>';
                $tabla .= '<td tipoError="ERROR" error="El tipo de identificación no existe o esta mal escrito" style="background:red;">'.$numeroIdentificacion.'</td>';
            }

            $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$direccion.'</td>';
            $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$ciudad.'</td>';
            $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$codigoPostal.'</td>';
            $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$pais.'</td>';

            if(strlen($latitud) > 0){
                $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$latitud.'</td>';
            }else{
                $tabla .= '<td tipoError="ADVERTENCIA" error="El campo de latitud esta vacio" style="background:yellow;">'.$latitud.'</td>';

            }

            if(strlen($longitud) > 0){
                $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$longitud.'</td>';
            }else{
                $tabla .= '<td tipoError="ADVERTENCIA" error="El campo de longitud esta vacio" style="background:yellow;">'.$longitud.'</td>';

            }
            
            $tabla .= '</tr>';

        }

        $output = array(
            'estado'     =>  'correcto',
            'mensaje'   => $tabla
        );

        echo json_encode($output);


    }
    
    public function documentos(Request $request)
    {
        $objPHPExcel = IOFactory::load($_FILES['excel']['tmp_name']);
        $objPHPExcel->setActiveSheetIndex(0);
        $numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
        $ultimaColumna = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
        $userId = Auth::id();
        $socio  = socios::where('correo_id','=',$userId)
                            ->first();
        
        $tabla = '<tr>';
        $tabla .= '<th>TIPO IDENTIFICACIÓN</th>';
        $tabla .= '<th>NUMERO DE IDENTIFICACIÓN</th>';
        $tabla .= '<th>TIPOS DE DOCUMENTOS</th>';
        $tabla .= '<th>NUMERO DE DOCUMENTO</th>';
        $tabla .= '<th>EMÍSION</th>';
        $tabla .= '<th>VENCIMIENTO</th>';
        $tabla .= '<th>TIPO DE MONEDA</th>';
        $tabla .= '<th>IMPORTE</th>';
        $tabla .= '</tr>';

        for ($i=2; $i <= $numRows; $i++) {
            $tabla .= '<tr>';

            $tipoIdentificacion     = $objPHPExcel->getActiveSheet()->getCell('a'.$i)->getCalculatedValue();
            $numeroIdentificacion   = $objPHPExcel->getActiveSheet()->getCell('b'.$i)->getCalculatedValue();
            $tipoDocumento          = $objPHPExcel->getActiveSheet()->getCell('c'.$i)->getCalculatedValue();
            $numeroDocumento        = $objPHPExcel->getActiveSheet()->getCell('d'.$i)->getCalculatedValue();
            $emision                = $objPHPExcel->getActiveSheet()->getCell('e'.$i)->getCalculatedValue();
            $vencimiento            = $objPHPExcel->getActiveSheet()->getCell('f'.$i)->getCalculatedValue();
            $tipoMoneda             = $objPHPExcel->getActiveSheet()->getCell('g'.$i)->getCalculatedValue();
            $importe                = $objPHPExcel->getActiveSheet()->getCell('h'.$i)->getCalculatedValue();


            $tiposDocumentosIdentidad = tiposDocumentosIdentidad::where('nombre','=',$tipoIdentificacion)
                                                                ->first();
            if($tiposDocumentosIdentidad){

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

                        for($x = 0; $x < sizeof($sectores); $x++ ){
                            
                            $exisCliente= clientes::where('correo_id', '=', $userId )
                                                ->where('sector_id','=',$sectores[$x]['id'])
                                                ->first();
                            if($exisCliente){
                                $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$tipoIdentificacion.'</td>';
                                $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$numeroIdentificacion.'</td>';
                                break;
                            }else if($x == sizeof($sectores)-1 ){
                                $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$tipoIdentificacion.'</td>';
                                $tabla .= '<td tipoError="ERROR" error="El cliente no existe o esta mal escrito" style="background:red;">'.$numeroIdentificacion.'</td>';
                                break;
                            }
                        }
                    }else{
                        $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$tipoIdentificacion.'</td>';
                        $tabla .= '<td tipoError="ERROR" error="El cliente no existe o esta mal escrito" style="background:red;">'.$numeroIdentificacion.'</td>';
                    }
                }else{
                    $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$tipoIdentificacion.'</td>';
                    $tabla .= '<td tipoError="ERROR" error="El numero de identificación no existe o esta mal escrito" style="background:red;">'.$numeroIdentificacion.'</td>';
                }

            }else{
                $tabla .= '<td tipoError="ERROR" error="Este tipo de identificación no existe o esta mal escrito" style="background:red;">'.$tipoIdentificacion.'</td>';
                $tabla .= '<td tipoError="ERROR" error="El tipo de identificación no existe o esta mal escrito" style="background:red;">'.$numeroIdentificacion.'</td>';
            }

            $siTipoDocumento = tiposDocumentos::where('nombre', $tipoDocumento)->first();
            if($siTipoDocumento){
                $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$tipoDocumento.'</td>';
            }else{
                $tabla .= '<td tipoError="ERROR" error="El tipo de documento no existe o esta mal escrito" style="background:red;">'.$tipoDocumento.'</td>';
            }

            $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$numeroDocumento.'</td>';

            $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$emision.'</td>';

            $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$vencimiento.'</td>';
           

            $siTiposMoneda = tiposMonedas::where('nombre', $tipoMoneda)->first();
            if($siTiposMoneda){
                $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$tipoMoneda.'</td>';
            }else{
                $tabla .= '<td tipoError="ERROR" error="El tipo de moneda no existe" style="background:red;">'.$tipoMoneda.'</td>';
            }

            if(is_numeric($importe)){
                $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$importe.'</td>';
            }else{
                $tabla .= '<td tipoError="ERROR" error="El importe del documento no es numerio" style="background:red;">'.$importe.'</td>';
            }

            $tabla .= '</tr>';

        }

        $output = array(
            'estado'     =>  'correcto',
            'mensaje'   => $tabla
        );

        echo json_encode($output);


    }

    public function pagos(Request $request)
    {
        $objPHPExcel = IOFactory::load($_FILES['excel']['tmp_name']);
        $objPHPExcel->setActiveSheetIndex(0);
        $numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
        $ultimaColumna = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
        $userId = Auth::id();
        $socio  = socios::where('correo_id','=',$userId)
                            ->first();
        
        $tabla = '<tr>';
        $tabla .= '<th>TIPO IDENTIFICACIÓN</th>';
        $tabla .= '<th>NUMERO DE IDENTIFICACIÓN</th>';
        $tabla .= '<th>TIPOS DE DOCUMENTOS</th>';
        $tabla .= '<th>NUMERO DE DOCUMENTO</th>';
        $tabla .= '<th>TIPOS DE PAGO</th>';
        $tabla .= '<th>NUMERO DE PAGO</th>';
        $tabla .= '<th>EMISIÓN</th>';
        $tabla .= '<th>IMPORTE</th>';
        $tabla .= '</tr>';

        for ($i=2; $i <= $numRows; $i++) {
            $tabla .= '<tr>';

            $tipoIdentificacion     = $objPHPExcel->getActiveSheet()->getCell('a'.$i)->getCalculatedValue();
            $numeroIdentificacion   = $objPHPExcel->getActiveSheet()->getCell('b'.$i)->getCalculatedValue();
            $tipoDocumento          = $objPHPExcel->getActiveSheet()->getCell('c'.$i)->getCalculatedValue();
            $numeroDocumento        = $objPHPExcel->getActiveSheet()->getCell('d'.$i)->getCalculatedValue();
            $tipoPago               = $objPHPExcel->getActiveSheet()->getCell('e'.$i)->getCalculatedValue();
            $numeroPago             = $objPHPExcel->getActiveSheet()->getCell('f'.$i)->getCalculatedValue();
            $emision                = $objPHPExcel->getActiveSheet()->getCell('g'.$i)->getCalculatedValue();
            $importe                = $objPHPExcel->getActiveSheet()->getCell('h'.$i)->getCalculatedValue();


            $tiposDocumentosIdentidad = tiposDocumentosIdentidad::where('nombre','=',$tipoIdentificacion)
                                                                ->first();
            if($tiposDocumentosIdentidad){

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

                        for($x = 0; $x < sizeof($sectores); $x++ ){
                            
                            $exisCliente= clientes::where('correo_id', '=', $userId )
                                                ->where('sector_id','=',$sectores[$x]['id'])
                                                ->first();
                            if($exisCliente){
                                $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$tipoIdentificacion.'</td>';
                                $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$numeroIdentificacion.'</td>';
                                
                                $siTipoDocumento = tiposDocumentos::where('nombre', $tipoDocumento)->first();
                                if($siTipoDocumento){
                                    $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$tipoDocumento.'</td>';
                                    $siDocumento = documentos::where('cliente_id', $exisCliente->id)
                                                                ->where('tipoDocumento_id', $siTipoDocumento->id)
                                                                ->where('numero', $numeroDocumento)
                                                                ->first();
                                    if($siDocumento){
                                        $tabla .= '<td tipoError="BIEN" error="Sin errores" style="background:red;">'.$numeroDocumento.'</td>';

                                    }else{
                                        $tabla .= '<td tipoError="ERROR" error="El numero de documento no existe" style="background:red;">'.$numeroDocumento.'</td>';
                                    }
                                }else{
                                    $tabla .= '<td tipoError="ERROR" error="El tipo de documento no existe o esta mal escrito" style="background:red;">'.$tipoDocumento.'</td>';
                                }
                                break;
                            }else if($x == sizeof($sectores)-1 ){
                                $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$tipoIdentificacion.'</td>';
                                $tabla .= '<td tipoError="ERROR" error="El cliente no existe o esta mal escrito" style="background:red;">'.$numeroIdentificacion.'</td>';
                                break;
                            }
                        }
                    }else{
                        $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$tipoIdentificacion.'</td>';
                        $tabla .= '<td tipoError="ERROR" error="El cliente no existe o esta mal escrito" style="background:red;">'.$numeroIdentificacion.'</td>';
                    }
                }else{
                    $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$tipoIdentificacion.'</td>';
                    $tabla .= '<td tipoError="ERROR" error="El numero de identificación no existe o esta mal escrito" style="background:red;">'.$numeroIdentificacion.'</td>';
                }

            }else{
                $tabla .= '<td tipoError="ERROR" error="Este tipo de identificación no existe o esta mal escrito" style="background:red;">'.$tipoIdentificacion.'</td>';
                $tabla .= '<td tipoError="ERROR" error="El tipo de identificación no existe o esta mal escrito" style="background:red;">'.$numeroIdentificacion.'</td>';
            }

            $siTipoPago = tipospagos::where('nombre', $tipoPago)->first();
            if($siTipoPago){
                $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$tipoPago.'</td>';
            }else{
                $tabla .= '<td tipoError="ERROR" error="El tipo de pago no existe o esta mal escrito" style="background:red;">'.$tipoPago.'</td>';
            }

            $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$numeroPago.'</td>';
            $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$emision.'</td>';


            if(is_numeric($importe)){
                $tabla .= '<td tipoError="BIEN" error="Sin errores">'.$importe.'</td>';
            }else{
                $tabla .= '<td tipoError="ERROR" error="El importe del documento no es numerio" style="background:red;">'.$importe.'</td>';
            }

            $tabla .= '</tr>';

        }

        $output = array(
            'estado'     =>  'correcto',
            'mensaje'   => $tabla
        );

        echo json_encode($output);


    }


}
