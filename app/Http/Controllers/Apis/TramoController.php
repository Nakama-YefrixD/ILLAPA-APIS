<?php

namespace App\Http\Controllers\Apis;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\empresas;
use App\socios;
use App\clientes;
use App\tramos;


class TramoController extends Controller
{
    public function mostrarEmpresas()
    {

        $empresas = empresas::select("empresas.nombre as empresaNombre", "u.email as userEmail", 
                                        "empresas.id as empresaId",
                                        "p.tipoDocumentoIdentidad_id as personaTipoIdentificacion",
                                        "p.numeroidentificacion as personaNumeroIdentificacion",
                                        "p.imagen as personaImagen")
                            ->join('users as u', 'u.id', '=', 'empresas.correo_id')
                            ->join('personas as p', 'p.id', '=', 'u.persona_id')
                            ->join('tiposDocumentosIdentidad as tdi', 'tdi.id', '=', 'p.tipoDocumentoIdentidad_id')
                            ->where('empresas.estado', '=', 1)
                            ->get();



        if (sizeof($empresas) > 0){
            return json_encode(array("code" => 1, "result"=>$empresas , "load"=>true));
        }else{
            return json_encode(array("code" => 0, "message"=>"No hay empresas !" , "load"=>true));
        }

    }

    public function mostrarSocios($empresaid)
    {

        $empresa = empresas::select("empresas.nombre as empresaNombre", "u.email as userEmail", 
                                            "empresas.id as empresaId",
                                            "p.tipoDocumentoIdentidad_id as personaTipoIdentificacion",
                                            "p.numeroidentificacion as personaNumeroIdentificacion",
                                            "p.imagen as personaImagen")
                                ->join('users as u', 'u.id', '=', 'empresas.correo_id')
                                ->join('personas as p', 'p.id', '=', 'u.persona_id')
                                ->join('tiposDocumentosIdentidad as tdi', 'tdi.id', '=', 'p.tipoDocumentoIdentidad_id')
                                ->where('empresas.id', '=', $empresaid)
                                ->first();

        $sociosEmpresa = socios::select("socios.id as socioId", "socios.empresa_id as empresaId", 
                                        "u.email as userEmail", "p.tipoDocumentoIdentidad_id as personaTipoIdentificacion", 
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
            return json_encode(array("code" => false, "message"=>"No hay empresas !", "empresa"=>$empresa , "load"=>true));
        }

    }

    public function mostrarTramos($socioId)
    {

        $socioEmpresa = socios::select("socios.id as socioId", "socios.empresa_id as empresaId",
                                        "u.email as userEmail", "p.tipoDocumentoIdentidad_id as personaTipoIdentificacion",
                                        "p.numeroidentificacion as personaNumeroIdentificacion",
                                        "socios.estado as socioEstado", "p.nombre as personaNombre", 
                                        "p.imagen as personaImagen")
                            ->join('users as u', 'u.id', '=', 'socios.correo_id')
                            ->join('personas as p', 'p.id', '=', 'u.persona_id')
                            ->join('tiposDocumentosIdentidad as tdi', 'tdi.id', '=', 'p.tipoDocumentoIdentidad_id')
                            ->where('socios.id', '=', $socioId)
                            ->first();

        $tramosSocio = tramos::where('socio_id', '=', $socioId)
                                    ->orderBy('inicio')
                                    ->get();

                           

        if (sizeof($tramosSocio) > 0){
            return json_encode(array("code" => true, "result"=>$tramosSocio, "socio"=>$socioEmpresa , "load"=>true ));
        }else{
            return json_encode(array("code" => false, "message"=>"No hay empresas !", "socio"=>$socioEmpresa , "load"=>true));
        }

    }

    public function agregarTramo(Request $request)
    {

        $socioId = $request->socioId;
        $nombreTramo = $request->nombre;
        $inicio = $request->desde;
        $fin = $request->hasta;

        $tramos = new tramos;
        $tramos->socio_id = $socioId;
        $tramos->nombre = $nombreTramo;
        $tramos->inicio = $inicio;
        $tramos->fin = $fin;
        $tramos->estado = 1;

        if($tramos->save()){
            return json_encode(true);
        }else{
            return json_encode(false);
        }

        
        

    }

    public function eliminarTramo(Request $request)
    {
        $idTramo = $request->idTramo;

        $tramo = tramos::find($idTramo);
        
        if($tramo->delete()){
            return json_encode(true);
        }else{
            return json_encode(false);
        }



    }
}
