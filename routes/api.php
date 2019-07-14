<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
   
    
    
});

// Route::get("/cursos", 'Apis\ApiController@curso');

// Route::get("/registrarget/{dni}/{email}/{pass}", 'Apis\RegisterController@registrarget');
Route::post("/registrarpost", 'Apis\RegisterController@registrarpost');
Route::post('/loginApi', 'Auth\LoginController@loginApi');

Route::get('tiposDocumentosIdentidad', 'Apis\globales@tiposDocumentosIdentidad');


// GESTION
Route::middleware('auth:api')->get('/empresasTodas', 'Apis\GestionController@mostrarEmpresas');
Route::middleware('auth:api')->get('/sociosTodos/{empresaid}', 'Apis\GestionController@mostrarSocios');
Route::middleware('auth:api')->get('/clientesTodos/{socioid}', 'Apis\GestionController@mostrarClientes');
Route::middleware('auth:api')->get('/sectoresTodos/{socioId}', 'Apis\GestionController@mostrarSectores');
Route::middleware('auth:api')->post('/filtroClientesSector', 'Apis\GestionController@filtroClientesSector');

Route::middleware('auth:api')->get('/documentosTodos/{clienteid}', 'Apis\GestionController@mostrarDocumentosCliente');
Route::middleware('auth:api')->get('/formularAccionCliente/{clienteid}', 'Apis\GestionController@formularAccionCliente');
Route::middleware('auth:api')->get('/datosCliente/{clienteid}', 'Apis\GestionController@datosCliente');

Route::middleware('auth:api')->post('/agregarAccion', 'Apis\GestionController@agregarAccion');



// GESTION FREE
Route::middleware('auth:api')
        ->get('gestionFree/clientesTodos/{sectoristaId}', 'Apis\Gestores\gestionFreeController@mostrarClientes');

        Route::middleware('auth:api')
        ->get('gestionFree/documentosTodos/{sectoristaId}', 'Apis\Gestores\gestionFreeController@todosDocumentos');

// VENCIMIENTOS
Route::middleware('auth:api')->get('vencimiento/empresa/fechas/{id}', 'Apis\VencimientosController@mostrarFechasEmpresa');
Route::middleware('auth:api')->get('vencimiento/socio/fechas/{id}', 'Apis\VencimientosController@mostrarFechasEmpresa');
Route::middleware('auth:api')->get('vencimiento/sectorista/fechas/{id}', 'Apis\VencimientosController@');
Route::middleware('auth:api')->get('vencimiento/gestor/fechas/{id}', 'Apis\VencimientosController@');
Route::middleware('auth:api')->get('vencimiento/free/fechas/{id}', 'Apis\Vencimientos\vencmientosFreeController@mostrarFechasSectoristasFree');
Route::middleware('auth:api')->get('vencimiento/admin/fechas/{id}', 'Apis\VencimientosController@');

Route::middleware('auth:api')
        ->get('vencimiento/empresa/fechasEspecifica/{id}', 'Apis\VencimientosController@fechasDocumentosEmpresa');
Route::middleware('auth:api')
        ->get('vencimiento/socio/fechasEspecifica/{id}', 'Apis\VencimientosController@fechasDocumentosEmpresa');
Route::middleware('auth:api')
        ->get('vencimiento/sectorista/fechasEspecifica/{id}', 'Apis\VencimientosController@fechasDocumentosEmpresa');
Route::middleware('auth:api')
        ->get('vencimiento/gestor/fechasEspecifica/{id}', 'Apis\VencimientosController@fechasDocumentosEmpresa');
Route::middleware('auth:api')
        ->get('vencimiento/free/fechasEspecifica/{id}', 'Apis\Vencimientos\vencmientosFreeController@documentosSectoristasFree');
Route::middleware('auth:api')
        ->get('vencimiento/admin/fechasEspecifica/{id}', 'Apis\VencimientosController@fechasDocumentosEmpresa');


// ESTADISTICAS

Route::middleware('auth:api')->get('estadistica/empresasTodas', 'Apis\EstadisticaController@mostrarEmpresas');
Route::middleware('auth:api')->get('estadistica/sociosTodos/{empresaid}', 'Apis\EstadisticaController@mostrarSocios');
Route::middleware('auth:api')->get('estadistica/clientesTodos/{socioid}', 'Apis\EstadisticaController@mostrarClientes');
Route::middleware('auth:api')->get('estadistica/cliente/{clienteid}', 'Apis\EstadisticaController@mostrarCliente');

// ESTADISTICAS FREE

Route::middleware('auth:api')
        ->get('estadisticasFree/clientesTodos/{sectoristaId}', 'Apis\Estadisticas\estadisticasFreeController@mostrarClientes');


Route::middleware('auth:api')
        ->get('estadisticasFree/estadisticasClientes/{sectoristaId}', 'Apis\Estadisticas\estadisticasFreeController@estadisticasClientes');

// DATOS
Route::middleware('auth:api')->get('dato/empresasTodas', 'Apis\DatoController@mostrarEmpresas');
Route::middleware('auth:api')->get('dato/sociosTodos/{empresaid}', 'Apis\DatoController@mostrarSocios');
Route::middleware('auth:api')->post('dato/buscarClientes', 'Apis\DatoController@buscarClientes');
Route::middleware('auth:api')->get('dato/sectoresTodos/{socioId}', 'Apis\DatoController@mostrarSectores');
Route::middleware('auth:api')->post('dato/agregarNuevoCliente', 'Apis\DatoController@agregarNuevoCliente');
Route::middleware('auth:api')->post('dato/editarCliente', 'Apis\DatoController@editarCliente');

Route::middleware('auth:api')->post('dato/agregarDocumento', 'Apis\DatoController@agregarDocumento');
Route::middleware('auth:api')->post('dato/agregarPago', 'Apis\DatoController@agregarPago');

Route::middleware('auth:api')->get('dato/clientesTodos/{socioid}', 'Apis\DatoController@mostrarClientes');
Route::middleware('auth:api')->get('dato/clienteDocumentos/{clienteid}', 'Apis\DatoController@mostrarClienteDocumentos');
Route::middleware('auth:api')->get('dato/clienteDocumentoPagos/{clienteid}', 'Apis\DatoController@mostrarClienteDocumentoPagos');



// DATOS FREE
Route::middleware('auth:api')
        ->get('datoFree/clientesTodos/{sectoristaId}', 'Apis\Datos\datosFreeController@mostrarClientes');

Route::middleware('auth:api')
        ->get('datoFree/sectoresTodos/{socioId}', 'Apis\Datos\datosFreeController@mostrarSectores');

Route::middleware('auth:api')
        ->get('datoFree/clienteDatos/{clienteId}', 'Apis\Datos\datosFreeController@clienteDatos');

Route::middleware('auth:api')
        ->post('datoFree/agregarImagenCliente', 'Apis\Datos\datosFreeController@agregarImagenCliente');



// USUARIOS

Route::middleware('auth:api')->get('usuario/empresasTodas', 'Apis\UsuarioController@mostrarEmpresas');
Route::middleware('auth:api')->get('usuario/sociosTodos/{empresaid}', 'Apis\UsuarioController@mostrarSocios');
Route::middleware('auth:api')->get('usuario/usuariosTodos/{socioid}', 'Apis\UsuarioController@mostrarUsuarios');

Route::middleware('auth:api')->get('usuario/gestor/sector/{gestorid}/{socioid}', 'Apis\UsuarioController@mostrarSectorGestor');
Route::middleware('auth:api')->post('usuario/gestor/eliminarSector', 'Apis\UsuarioController@eliminarSectorGestor');


Route::middleware('auth:api')->get('usuario/sectorista/sector/{sectoristaid}', 'Apis\UsuarioController@mostrarSectorGestor');

Route::middleware('auth:api')->get('usuario/datoSocioEmpresaSectores/{socioid}', 'Apis\UsuarioController@mostrarSocioEmpresaSectores');

Route::middleware('auth:api')->post('usuario/usuarioValidar', 'Apis\UsuarioController@validarCorreo');

Route::middleware('auth:api')->post('usuario/usuarioValidarSocio', 'Apis\UsuarioController@validarCorreoSocio');

Route::middleware('auth:api')->post('usuario/agregarEmpresa', 'Apis\UsuarioController@agregarEmpresa');
Route::middleware('auth:api')->post('usuario/agregarSocio', 'Apis\UsuarioController@agregarSocio');
Route::middleware('auth:api')->post('usuario/agregarSector', 'Apis\UsuarioController@agregarSector');
Route::middleware('auth:api')->post('usuario/agregarGestor', 'Apis\UsuarioController@agregarGestor');
Route::middleware('auth:api')->post('usuario/agregarSectorista', 'Apis\UsuarioController@agregarSectorista');
// Route::middleware('auth:api')->get('usuario/clienteDocumentoPagos/{clienteid}', 'Apis\UsuarioController@mostrarClienteDocumentoPagos');


Route::middleware('auth:api')->get('tramo/empresasTodas', 'Apis\TramoController@mostrarEmpresas');
Route::middleware('auth:api')->get('tramo/sociosTodos/{empresaid}', 'Apis\TramoController@mostrarSocios');
Route::middleware('auth:api')->get('tramo/tramosSocio/{socioid}', 'Apis\TramoController@mostrarTramos');

Route::middleware('auth:api')->post('tramo/agregarTramo', 'Apis\TramoController@agregarTramo');
Route::middleware('auth:api')->post('tramo/eliminarTramo', 'Apis\TramoController@eliminarTramo');


