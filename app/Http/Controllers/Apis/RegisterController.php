<?php

namespace App\Http\Controllers\Apis;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\personas;
use App\User;
use App\socios;
use App\sectoristas;
use App\sectores;
use App\gestores;

use Peru\Jne\Dni;
use Peru\Sunat\Ruc;
use Peru\Http\ContextClient;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function registrarget($dni, $email, $pass)
    {

        $personas = new personas;
        $personas->tipoidentificacion = 1;
        $personas->numeroidentificacion = $dni;
        $personas->nombre = ''.$email.'';
        $personas->imagen = 'imagen';
        $personas->estado = '1';
        $personas->save();

        User::create([
            'persona_id' => $personas->id ,
            'email' => ''.$email.'',
            'estado' => 1,
            'password' => Hash::make($pass),
            'api_token' => Str::random(60),

            
        ]);

        return $personas->id;
    }

    public function registrarpost(Request $request)
    {
        $tipoIdentificacion = $request->tipoIdentificacion;
        $nombreAgregado = $request->nombre;
        $dni = $request->dni;
        $email = $request->email;
        $pass = $request->pass;

        
        $siPersona = personas::where('numeroidentificacion', '=', $dni)
                            ->first();

        if($siPersona){

            $personaId = $siPersona->id;
        }else{
            if($tipoIdentificacion == 1 ){
                $cs = new Dni();
                $cs->setClient(new ContextClient());
    
                $person = $cs->get($dni);
                if ($person === false) {
                    $nombre = $email;
                    
                }else{
                    $nombre = $person->nombres." ".$person->apellidoPaterno." ".$person->apellidoMaterno;
                }
                
    
            }else if($tipoIdentificacion == 2){
                $cs = new Ruc();
                $cs->setClient(new ContextClient());
    
                $company = $cs->get($dni);
                if ($company === false) {
                    $nombre = $email;
                    
                }else{
                    $nombre = $company->razonSocial;
                }
                
                
    
            }else{
                $nombre = $nombreAgregado;
            }


            $personas = new personas;
            $personas->tipoDocumentoIdentidad_id = $tipoIdentificacion;
            $personas->numeroidentificacion = $dni;
            $personas->nombre = $nombre;
            // $personas->imagen = null;
            $personas->estado = '1';
            $personas->save();
            $personaId = $personas->id;
        }

        $user = User::create([
                    'persona_id' => $personaId,
                    'email' => ''.$email.'',
                    'estado' => 1,
                    'password' => Hash::make($pass),
                    'api_token' => Str::random(60),
                    
                ]);
        
        
            
        $sectorista = new sectoristas;
        $sectorista->socio_id = 1;
        $sectorista->correo_id = $user->id;
        $sectorista->estado = 1;
        $sectorista->save();

        $sector = new sectores;
        $sector->socio_id = 1;
        $sector->sectorista_id = $sectorista->id;
        $sector->descripcion = 'FREE';
        $sector->estado = 1;
        $sector->estSectorista = 1;
        $sector->estGestor = 1;
        $sector->save();
        
        $gestor = new gestores;
        $gestor->sector_id = $sector->id;
        $gestor->correo_id = $user->id;
        $gestor->estado = 1;
        $gestor->save();


        $apitoken = $user->api_token;
        

        return json_encode($apitoken);

    }
}
