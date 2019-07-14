<?php

namespace App\Http\Controllers\Apis;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\empresas;
use App\socios;
use App\clientes;
use App\User;
use App\sectoristas;
use App\sectores;
use App\gestores;

class UsuarioController extends Controller
{
    public function validarCorreo(Request $request)
    {
        $email = $request->email;

        $user = User::where('email', '=', $email)
                            ->first();
        if($user){

            $persona = User::select("users.email as userEmail", 
                                            "p.imagen as personaImagen", 
                                            "p.nombre as personaNombre", 
                                            "p.tipoidentificacion as personaTipoIdentificacion",
                                            "p.numeroidentificacion as personaNumeroIdentificacion")
                                ->join('personas as p', 'p.id', '=', 'users.persona_id')
                                ->where('users.id', '=', $user->id)
                                ->first();

            $personaEmpresaDefault = sectoristas::where('socio_id', '=', 1)
                                            ->where('correo_id', '=', $user->id)
                                            ->where('estado', '=', 1)
                                            ->first();
            if($personaEmpresaDefault){
                return json_encode(array("code" => 1,  "resultUser"=>$persona, "pertenece"=>false, "idSectoristaValidado"=>$personaEmpresaDefault->id ,"loadValidar"=>true));
            }else{
                return json_encode(array("code" => 1,  "resultUser"=>$persona, "pertenece"=>true ,"loadValidar"=>true));
            }

        }else{
            return json_encode(array("code" => 0,  "resultUser"=>$user, "loadValidar"=>true));
        }

    }

    public function agregarEmpresa(Request $request)
    {
        $idSectorista = $request->idSectoristaValido;
        $nombreEmpresa = $request->nombreEmpresa;

        $sectorista = sectoristas::find($idSectorista);
        $sectorista->estado = 0;
        $sectorista->update();

        $sector = sectores::where('sectorista_id', '=', $sectorista->id )->first();
        $sector->estado = 0;
        $sector->update();

        $gestor = gestores::where('sector_id', '=', $sector->id)->first();
        $gestor->estado = 0;
        $gestor->update();

        $empresa = new empresas;
        $empresa->correo_id = $sectorista->correo_id;
        $empresa->nombre = $nombreEmpresa;
        $empresa->estado = 1;

        if($empresa->save()){
            return json_encode(true);
        }else{
            return json_encode(false);
        }

    }

    public function agregarSocio(Request $request)
    {
        $idSectorista = $request->idSectoristaValido;
        $idEmpresaSeleccionada = $request->idEmpresaSeleccionada;

        $sectorista = sectoristas::find($idSectorista);
        $sectorista->estado = 0;
        $sectorista->update();

        $sector = sectores::where('sectorista_id', '=', $sectorista->id )->first();
        $sector->estado = 0;
        $sector->update();

        $gestor = gestores::where('sector_id', '=', $sector->id)->first();
        $gestor->estado = 0;
        $gestor->update();

        $socio = new socios;
        $socio->empresa_id = $idEmpresaSeleccionada;
        $socio->correo_id = $sectorista->correo_id;
        $socio->estado = 1;

        if($socio->save()){
            return json_encode(true);
        }else{
            return json_encode(false);
        }

    }

    public function agregarSector(Request $request)
    {
        $idSocio = $request->idSocio;
        $descripcion = $request->descripcion;

        $sector = new sectores;
        $sector->socio_id = $idSocio;
        $sector->sectorista_id = null;
        $sector->descripcion = $descripcion;
        $sector->estado = 1;
        $sector->estSectorista = 0;
        $sector->estGestor = 0;

        if($sector->save()){
            return json_encode(true);
        }else{
            return json_encode(false);
        }
    }

    public function agregarGestor(Request $request)
    {
        $idSector = $request->idSector;
        $idSectorista = $request->idSectorista;


        $sectorista = sectoristas::find($idSectorista);
        $sectorista->estado = 0;
        $sectorista->update();

        $sector = sectores::where('sectorista_id', '=', $idSectorista )->first();
        $sector->estado = 0;
        $sector->update();

        $gestor = gestores::where('sector_id', '=', $sector->id)->first();
        $gestor->estado = 0;
        $gestor->update();

        $gestor = new gestores;
        $gestor->sector_id = $idSector;
        $gestor->correo_id = $sectorista->correo_id;
        $gestor->estado = 1;

        $sectorUpdate = sectores::where('id', '=', $idSector)->first();
        $sectorUpdate->estGestor = 1;
        $sectorUpdate->update();

        if($gestor->save()){
            return json_encode(true);
        }else{
            return json_encode(false);
        }

    }




    public function agregarSectorista(Request $request)
    {
        $idSocio = $request->idSocio;
        $idsSectores = $request->idsSectores;
        $idSectorista = $request->idSectorista;

        $sectorista = sectoristas::find($idSectorista);
        $sectorista->estado = 0;
        $sectorista->update();

        $sector = sectores::where('sectorista_id', '=', $idSectorista )->first();
        $sector->estado = 0;
        $sector->update();

        $gestor = gestores::where('sector_id', '=', $sector->id)->first();
        $gestor->estado = 0;
        $gestor->update();

        $sectoristaNuevo = new sectoristas;
        $sectoristaNuevo->socio_id = $idSocio;
        $sectoristaNuevo->correo_id = $sectorista->correo_id;
        $sectoristaNuevo->estado = 1;

        

        if($sectoristaNuevo->save()){
            
            $sectores = explode("-", $idsSectores);
            $longSectores = sizeof($sectores);
            for($x = 1; $x <= $longSectores; $x++){
                
                $sectorUpdate = sectores::where('id', '=', $sectores[$x])->first();
                $sectorUpdate->estSectorista = 1;
                $sectorUpdate->sectorista_id = $sectoristaNuevo->id;
                $sectorUpdate->update();


            }
            

            return json_encode(true);
        }else{
            return json_encode(false);
        }

    }




    public function validarCorreoSocio(Request $request)
    {
        $email = $request->email;
        $idEmpresaSeleccionada = $request->idEmpresaSeleccionada;

        $user = User::where('email', '=', $email)
                            ->first();
                            
        if($user){

            $persona = User::select("users.email as userEmail", 
                                            "p.imagen as personaImagen", 
                                            "p.nombre as personaNombre", 
                                            "p.tipoidentificacion as personaTipoIdentificacion",
                                            "p.numeroidentificacion as personaNumeroIdentificacion")
                                ->join('personas as p', 'p.id', '=', 'users.persona_id')
                                ->where('users.id', '=', $user->id)
                                ->first();

            $personaEmpresaDefault = sectoristas::where('socio_id', '=', 1)
                                            ->where('correo_id', '=', $user->id)
                                            ->where('estado', '=', 1)
                                            ->first();
            if($personaEmpresaDefault){
            
                return json_encode(array("code" => 1,  "resultUser"=>$persona, "pertenece"=>false, "idSectoristaValidado"=>$personaEmpresaDefault->id ,"loadValidar"=>true));
            
            }else{
                $socio = socios::where('empresa_id', '=', $idEmpresaSeleccionada)
                                ->where('correo_id', '=',  $user->id)
                                ->first();
                if($socio){
                    return json_encode(array("code" => 1,  "resultUser"=>$persona, "pertenece"=>true, "siAsociado"=>true , "loadValidar"=>true));
                    
                }else{
                    return json_encode(array("code" => 1,  "resultUser"=>$persona, "pertenece"=>true, "siAsociado"=>false , "loadValidar"=>true));
                    
                }
            }

        }else{
            return json_encode(array("code" => 0,  "resultUser"=>$user, "loadValidar"=>true));
        }

    }


    public function mostrarEmpresas()
    {

        $empresas = empresas::select("empresas.nombre as empresaNombre", "u.email as userEmail", 
                                        "empresas.id as empresaId",
                                        "p.tipoidentificacion as personaTipoIdentificacion", 
                                        "p.numeroidentificacion as personaNumeroIdentificacion",
                                        "p.imagen as personaImagen")
                            ->join('users as u', 'u.id', '=', 'empresas.correo_id')
                            ->join('personas as p', 'p.id', '=', 'u.persona_id')
                            ->where('empresas.estado', '=', 1)
                            ->get();



        if (sizeof($empresas) > 0){
            return json_encode(array("code" => true, "result"=>$empresas , "load"=>true));
        }else{
            return json_encode(array("code" => false, "load"=>true));
        }

    }

    public function mostrarSocios($empresaid)
    {

        $empresa = empresas::select("empresas.nombre as empresaNombre", "u.email as userEmail", 
                                            "empresas.id as empresaId",
                                            "p.tipoidentificacion as personaTipoIdentificacion", 
                                            "p.numeroidentificacion as personaNumeroIdentificacion",
                                            "p.imagen as personaImagen")
                                ->join('users as u', 'u.id', '=', 'empresas.correo_id')
                                ->join('personas as p', 'p.id', '=', 'u.persona_id')
                                ->where('empresas.id', '=', $empresaid)
                                ->first();

        $sociosEmpresa = socios::select("socios.id as socioId", "socios.empresa_id as empresaId", 
                                        "u.email as userEmail", "p.tipoidentificacion as personaTipoIdentificacion", 
                                        "p.numeroidentificacion as personaNumeroIdentificacion",
                                        "socios.estado as socioEstado", "p.nombre as personaNombre", 
                                        "p.imagen as personaImagen")
                            ->join('users as u', 'u.id', '=', 'socios.correo_id')
                            ->join('personas as p', 'p.id', '=', 'u.persona_id')
                            ->where('socios.empresa_id', '=', $empresaid)
                            ->get();

                            // select s.id, s.empresa_id, s.estado, p.nombre, p.imagen
                            // from socios s, personas p, users u
                            // where s.correo_id = u.id && u.persona_id = p.id && empresa_id = ;

        if (sizeof($sociosEmpresa) > 0){
            return json_encode(array("code" => true, "result"=>$sociosEmpresa, "empresa"=>$empresa, "load"=>true  ));
        }else{
            return json_encode(array("code" => false, "message"=>"No hay empresas !", "empresa"=>$empresa, "load"=>true));
        }

    }


    public function mostrarUsuarios($socioId)
    {

        $socioEmpresa = socios::select("socios.id as socioId", "socios.empresa_id as empresaId",
                                        "u.email as userEmail", "p.tipoidentificacion as personaTipoIdentificacion",
                                        "p.numeroidentificacion as personaNumeroIdentificacion",
                                        "socios.estado as socioEstado", "p.nombre as personaNombre", 
                                        "p.imagen as personaImagen")
                            ->join('users as u', 'u.id', '=', 'socios.correo_id')
                            ->join('personas as p', 'p.id', '=', 'u.persona_id')
                            ->where('socios.id', '=', $socioId)
                            ->first();


        $sectoristasSocio = socios::select("socios.id as socioId",  'scts.id as sectoristasId',
                                        "u.email as userEmail", "p.tipoidentificacion as personaTipoIdentificacion", 
                                        "p.numeroidentificacion as personaNumeroIdentificacion",
                                        "p.nombre as personaNombre", 
                                        "p.imagen as personaImagen")
                            ->join('sectoristas as scts', 'scts.socio_id', '=', 'socios.id')
                            ->join('users as u', 'u.id', '=', 'scts.correo_id')
                            ->join('personas as p', 'p.id', '=', 'u.persona_id')
                            ->where('socios.id', '=', $socioId)
                            ->get();

                                //select s.id as socioId, scts.id as sectoristaId
                                // from socios s, sectoristas scts
                                // where s.id = scts.socio_id;


        $gestoresSocio = socios::select("socios.id as socioId",  'g.id as gestorId',
                                            "u.email as userEmail", "p.tipoidentificacion as personaTipoIdentificacion", 
                                            "p.numeroidentificacion as personaNumeroIdentificacion",
                                            "p.nombre as personaNombre", 
                                            "p.imagen as personaImagen")

                                // ->join('sectoristas as scts', 'scts.socio_id', '=', 'socios.id')
                                ->join('sectores as sct', 'sct.socio_id', '=', 'socios.id')
                                ->join('gestores as g', 'g.sector_id', '=', 'sct.id')
                                ->join('users as u', 'u.id', '=', 'g.correo_id')
                                ->join('personas as p', 'p.id', '=', 'u.persona_id')
                                
                                ->where('socios.id', '=', $socioId)
                                ->get();

            // select s.id as socioId, scts.id as sectoristasId, g.id as gestorId, sct.id as sectorId
            // from socios s, sectoristas scts, gestores g, sectores sct
            // where scts.socio_id = s.id && g.sector_id = sct.id && sct.sectorista_id = scts.id && s.id =  ;



        if (sizeof($sectoristasSocio) > 0 || sizeof($gestoresSocio) > 0 ){
            return json_encode(array("code" => true, "resultSectorista"=>$sectoristasSocio, "resultGestores"=>$gestoresSocio ,"socio"=>$socioEmpresa, "load"=>true  ));
        }else{
            return json_encode(array("code" => false, "socio"=>$socioEmpresa, "load"=>true));
        }

    }

    public function mostrarSectorGestor($gestorId, $socioid)
    {

        // $gestor = gestores::find($gestorId);
        $gestorSector = gestores::select("sct.descripcion as sectoresDescripcion", "sct.id as sectorId")
                            ->join('sectores as sct', 'sct.id', '=', 'gestores.sector_id')
                            ->where('sct.estado', '=', 1)
                            ->where('gestores.id', '=', $gestorId)
                            ->get();

        $sectoresSocio = sectores::select("sectores.descripcion as sectoresDescripcion",
                                            "sectores.id as id",
                                            "sectores.estGestor as estGestor")
                            ->where('sectores.socio_id', '=', $socioid)
                            ->get();

        if (sizeof($gestorSector) > 0 ){
            return json_encode(array("code" => true, "sectores"=>$sectoresSocio, "sectoresSeleccionados"=>$gestorSector ,"load"=>true  ));
        }else{
            return json_encode(array("code" => false,  "load"=>true));
        }

    }

    public function eliminarSectorGestor(Request $request)
    {

    
        $idGestor = $request->idgestor;
        $idSectorAntiguo = $request->idsectorAntiguo;
        $idSectorNuevo = $request->idsectorNuevo;
        $idSocio= $request->idsocio;
        
        $sectorAntiguo = sectores::find($idSectorAntiguo);
        $sectorAntiguo->estGestor = 0;
        $sectorAntiguo->update();

        $gestor = gestores::find($idGestor);
        $gestor->sector_id = $idSectorNuevo;
        $gestor->update();

        $sectorNuevo = sectores::find($idSectorNuevo);
        $sectorNuevo->estGestor = 1;

        if( $sectorNuevo->update()){
            return json_encode(array("code" => true,  "load"=>true));
        }else{
            return json_encode(array("code" => false, "load"=>true));
        }


    }



    public function mostrarSocioEmpresaSectores($socioId)
    {
        $socioEmpresa = socios::select("socios.id as socioId", "socios.empresa_id as empresaId",
                                            "u.email as userEmail", "p.tipoidentificacion as personaTipoIdentificacion",
                                            "p.numeroidentificacion as personaNumeroIdentificacion",
                                            "socios.estado as socioEstado", "p.nombre as personaNombre", 
                                            "p.imagen as personaImagen")
                                ->join('users as u', 'u.id', '=', 'socios.correo_id')
                                ->join('personas as p', 'p.id', '=', 'u.persona_id')
                                ->where('socios.id', '=', $socioId)
                                ->first();

        $empresa = empresas::select("empresas.nombre as empresaNombre", "u.email as userEmail", 
                                            "empresas.id as empresaId",
                                            "p.tipoidentificacion as personaTipoIdentificacion", 
                                            "p.numeroidentificacion as personaNumeroIdentificacion")
                                ->join('users as u', 'u.id', '=', 'empresas.correo_id')
                                ->join('personas as p', 'p.id', '=', 'u.persona_id')
                                ->where('empresas.id', '=', $socioEmpresa->empresaId)
                                ->first();
        
        $sectores = sectores::where('socio_id', '=', $socioId)->get();

        return json_encode(array("code" => true, "socio"=>$socioEmpresa, "empresa"=>$empresa ,"load"=>true, "sectores"=>$sectores   ));
    
        

        

    }

}
