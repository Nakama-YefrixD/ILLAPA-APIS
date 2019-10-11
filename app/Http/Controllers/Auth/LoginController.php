<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
use App\empresas;
use App\socios;
use App\sectoristas;
use App\gestores;
use App\User;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required',
            'password' => 'required',
        ]);

        $username = $request->email;
        $password = $request->password;
        
        $validacion = User::where('email', '=', $username)
                            ->where('estado','=',1) 
                            ->first();
        $siSocio = socios::where('correo_id','=',$validacion->id)
                                ->where('estado','=',1)
                                ->first();
        if($siSocio){

            if(filter_var($username, FILTER_VALIDATE_EMAIL)) {
                if (Auth::attempt(['email' => $username, 'password' => $password])) {
                    return redirect()->intended($this->redirectPath());
                }
                return redirect()->back()
                ->withInput()
                ->withErrors([
                    'login' => 'These credentials do not match our records.',
                ]);
            } else {
                if(Auth::attempt(['codigo' => $username, 'password' => $password])) {
                    return redirect()->intended($this->redirectPath());
                }
                return redirect()->back()
                ->withInput()
                ->withErrors([
                    'login' => 'These credentials do not match our records.',
                ]);
            }
        }else{
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'login' => 'These credentials do not match our records.',
                ]);
        }

        
    }

    public function loginApi(Request $request)
    {
        $username = $request->email;
        $password = $request->pass;

        
        if(filter_var($username, FILTER_VALIDATE_EMAIL)) {
            if (Auth::attempt(['email' => $username, 'password' => $password])) {
                $user = $this->guard()->user();
                $user->api_token;

                $siAdmin = empresas::where('correo_id', '=', $user->id)
                                    ->where('id', '=', 1)
                                    ->where('nombre', '=', 'ILLAPA')
                                    ->first();

                $persona = User::select( "p.imagen as personaImagen",
                                            "p.nombre as personaNombre",
                                            "users.email_verified_at as email_verified_at" )
                                ->join('personas as p', 'p.id', '=', 'users.persona_id')
                                ->where('users.id', '=', $user->id)
                                ->first();

                
                if($persona->email_verified_at == null){
                    $verificado = false;
                }else{
                    $verificado = true;
                }

                if ($siAdmin){

                    return json_encode(
                        array(
                            "code" => 1, 
                            "api_token"=>$user->api_token, 
                            "tipoUsuario"=>99, "id" =>$siAdmin->id , 
                            "nombreLogeado"=>$persona->personaNombre, 
                            "imagenLogeado"=>$persona->personaImagen,
                            "verificado"    =>$verificado,

                        )
                    );
                }


                $siEmpresa = empresas::where('correo_id', '=', $user->id)
                            ->where('estado','=','1')
                            ->first();

                if ($siEmpresa){

                    return json_encode(
                        array(
                            "code" => 1, 
                            "api_token"=>$user->api_token, 
                            "tipoUsuario"=>1, 
                            "id" =>$siEmpresa->id, 
                            "nombreLogeado"=>$persona->personaNombre, 
                            "imagenLogeado"=>$persona->personaImagen,
                            "verificado"    =>$verificado,
                        )
                    );
                }

                $siSocioFree = sectoristas::where('socio_id', '=', 1)
                                    ->where('correo_id', '=', $user->id)
                                    ->where('estado', '=', 1)
                                    ->first();
                if ($siSocioFree){

                    return json_encode(
                        array(
                            "code" => 1, 
                            "api_token"=>$user->api_token, 
                            "tipoUsuario"=>5, 
                            "id" =>$siSocioFree->id, 
                            "nombreLogeado"=>$persona->personaNombre, 
                            "imagenLogeado"=>$persona->personaImagen,
                            "verificado"    =>$verificado,
                        )
                    );
                }

                $siSocio = socios::where('correo_id', '=', $user->id)
                        ->where('estado','=','1')
                        ->first();
                if ($siSocio){
                    return json_encode(
                        array(
                            "code" => 1, 
                            "api_token"=>$user->api_token, 
                            "tipoUsuario"=>2, 
                            "id" =>$siSocio->id, 
                            "nombreLogeado"=>$persona->personaNombre, 
                            "imagenLogeado"=>$persona->personaImagen,
                            "verificado"    =>$verificado,
                        )
                    );
                }
                $siSectorista = sectoristas::where('correo_id', '=', $user->id)
                        ->where('estado','=','1')
                        ->first();
                        
                if ($siSectorista){
                    return json_encode(
                        array(
                            "code" => 1, 
                            "api_token"=>$user->api_token, 
                            "tipoUsuario"=>3, 
                            "id" =>$siSectorista->id, 
                            "nombreLogeado"=>$persona->personaNombre, 
                            "imagenLogeado"=>$persona->personaImagen,
                            "verificado"    =>$verificado,
                        )
                    );
                }

                $siGestor = gestores::where('correo_id', '=', $user->id)
                                        ->where('estado','=','1')
                                        ->first();
                if ($siGestor){
                                
                    return json_encode(
                        array(
                            "code" => 1, 
                            "api_token"=>$user->api_token, 
                            "tipoUsuario"=>4, 
                            "id" =>$siGestor->id, 
                            "nombreLogeado"=>$persona->personaNombre, 
                            "imagenLogeado"=>$persona->personaImagen,
                            "verificado"    =>$verificado,
                        )
                    );
                    
                }else{

                    return json_encode(
                        array(
                            "code" => 1, 
                            "api_token"=>$user->api_token, 
                            "tipoUsuario"=>99, 
                            "id" =>99, 
                            "imagenLogeado"=>$persona->personaImagen,
                            "verificado"    =>$verificado,
                        )
                    );
                }
                
                
                
            }
            return response()->json(false);
        } else {
            if(Auth::attempt(['codigo' => $username, 'password' => $password])) {
                $user = $this->guard()->user();
                $user->api_token();

                return response()->json([
                    $user,
                    true
                ]);
            }
            return response()->json(false);
        }

        // return $this->sendFailedLoginResponse($request);
        return response()->json([false]);
    }

    public function loginSocialityApi(Request $request)
    {
        $username = $request->email;
        $password = 'FacebokLogin';

        $correo = User::select( "users.email" )
                            ->where('users.email', '=', $username)
                            ->first();
        if(!$correo){
            return json_encode(array("code" => false));
        }
        

        if(filter_var($username, FILTER_VALIDATE_EMAIL)) {
            if (Auth::attempt(['email' => $username, 'password' => $password])) {
                $user = $this->guard()->user();
                $user->api_token;

                $siAdmin = empresas::where('correo_id', '=', $user->id)
                                    ->where('id', '=', 1)
                                    ->where('nombre', '=', 'ILLAPA')
                                    ->first();

                $persona = User::select( "p.imagen as personaImagen",
                                             "p.nombre as personaNombre" )
                                ->join('personas as p', 'p.id', '=', 'users.persona_id')
                                ->where('users.id', '=', $user->id)
                                ->first();

                if ($siAdmin){

                    return json_encode(array("code" => true, "api_token"=>$user->api_token, "tipoUsuario"=>99, "id" =>$siAdmin->id , "nombreLogeado"=>$persona->personaNombre, "imagenLogeado"=>$persona->personaImagen ));
                }


                $siEmpresa = empresas::where('correo_id', '=', $user->id)
                            ->where('estado','=','1')
                            ->first();

                if ($siEmpresa){

                    return json_encode(array("code" => true, "api_token"=>$user->api_token, "tipoUsuario"=>1, "id" =>$siEmpresa->id  , "nombreLogeado"=>$persona->personaNombre, "imagenLogeado"=>$persona->personaImagen ));
                }

                $siSocioFree = sectoristas::where('socio_id', '=', 1)
                                    ->where('correo_id', '=', $user->id)
                                    ->where('estado', '=', 1)
                                    ->first();
                if ($siSocioFree){

                    return json_encode(array("code" => true, "api_token"=>$user->api_token, "tipoUsuario"=>5, "id" =>$siSocioFree->id, "nombreLogeado"=>$persona->personaNombre, "imagenLogeado"=>$persona->personaImagen ));
                }

                $siSocio = socios::where('correo_id', '=', $user->id)
                        ->where('estado','=','1')
                        ->first();
                if ($siSocio){
                    return json_encode(array("code" => true, "api_token"=>$user->api_token, "tipoUsuario"=>2, "id" =>$siSocio->id, "nombreLogeado"=>$persona->personaNombre, "imagenLogeado"=>$persona->personaImagen ));
                }
                $siSectorista = sectoristas::where('correo_id', '=', $user->id)
                        ->where('estado','=','1')
                        ->first();
                        
                if ($siSectorista){
                    return json_encode(array("code" => true, "api_token"=>$user->api_token, "tipoUsuario"=>3, "id" =>$siSectorista->id, "nombreLogeado"=>$persona->personaNombre, "imagenLogeado"=>$persona->personaImagen ));
                }

                $siGestor = gestores::where('correo_id', '=', $user->id)
                                        ->where('estado','=','1')
                                        ->first();
                if ($siGestor){
                                
                    return json_encode(array("code" => true, "api_token"=>$user->api_token, "tipoUsuario"=>4, "id" =>$siGestor->id, "nombreLogeado"=>$persona->personaNombre, "imagenLogeado"=>$persona->personaImagen ));
                    
                }else{

                    return json_encode(array("code" => true, "api_token"=>$user->api_token, "tipoUsuario"=>99, "id" =>99, "imagenLogeado"=>$persona->personaImagen));
                }
                
                
                
            }
            return response()->json(false);
        } else {
            if(Auth::attempt(['codigo' => $username, 'password' => $password])) {
                $user = $this->guard()->user();
                $user->api_token();

                return response()->json([
                    $user,
                    true
                ]);
            }
            return response()->json(false);
        }

        // return $this->sendFailedLoginResponse($request);
        return response()->json([false]);
    }

    public function logoutApi(Request $request)
    {
        $user = Auth::guard('api')->user();

        if ($user) {
            $user->api_token = null;
            $user->save();
        }

        return response()->json(['data' => 'Cerró su sesión correctamente.'], 200);
    }

    


}
