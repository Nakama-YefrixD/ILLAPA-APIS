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


// REENVIAR CORREO DE CONFIRMACION 
Route::get(
        'reenviar/correo/confirmacion/{email}', 
        'Apis\Auth\correoConfirmacionController@reenviarCorreoConfirmacion'
);


Route::post("/registrarpost", 'Apis\RegisterController@registrarpost');
Route::post('/loginApi', 'Auth\LoginController@loginApi');
Route::post('/loginSocialityApi', 'Auth\LoginController@loginSocialityApi');

Route::get('tiposDocumentosIdentidad', 'Apis\globales@tiposDocumentosIdentidad');


// GESTION
Route::middleware('auth:api')->get('/empresasTodas', 'Apis\GestionController@mostrarEmpresas');

// FILTRO MAYOR
Route::middleware('auth:api')->get('/empresasTodas/filtroMayor', 'Apis\GestionController@filtroMayorAdm');
Route::middleware('auth:api')->get('/filtroMayor/empresas/{idEmpresa}', 'Apis\GestionController@filtroMayorEmp');
Route::middleware('auth:api')->get('/filtroMayor/sectorista/{idSectorista}', 'Apis\GestionController@filtroMayorSec');



Route::middleware('auth:api')->get('/sociosTodos/{empresaid}', 'Apis\GestionController@mostrarSocios');
Route::middleware('auth:api')->get('/clientesTodos/{socioid}', 'Apis\GestionController@mostrarClientes');
Route::middleware('auth:api')->get('/sectoresTodos/{socioId}', 'Apis\GestionController@mostrarSectores');
Route::middleware('auth:api')->post('/filtroClientesSector', 'Apis\GestionController@filtroClientesSector');

Route::middleware('auth:api')->get('/documentosTodos/{clienteid}', 'Apis\GestionController@mostrarDocumentosCliente');
Route::middleware('auth:api')->get('/formularAccionCliente/{clienteid}', 'Apis\GestionController@formularAccionCliente');
Route::middleware('auth:api')->get('/datosCliente/{clienteid}', 'Apis\GestionController@datosCliente');

Route::middleware('auth:api')->post('/agregarAccion', 'Apis\GestionController@agregarAccion');

// MOSTRAR CLIENTES GESTOR DE EMPRESAS
Route::middleware('auth:api')->get('gestion/clientesTodos/{gestorId}', 'Apis\Gestores\gestorEmpresasController@mostrarClientes');
Route::middleware('auth:api')->get('gestion/documentosTodos/{gestorId}', 'Apis\Gestores\gestorEmpresasController@todosDocumentos');
Route::middleware('auth:api')
        ->get('gestion/clientesTodosExcepcion/{gestorId}', 'Apis\Gestores\gestorEmpresasController@mostrarClientesTODO');

// GESTION DE SECTORISTAS
        // MOSTRAR LOS SECTORES DE UN SECTORISTA
        Route::middleware('auth:api')->get('sectorista/gestion/sectores/{sectoristaId}', 'Apis\Gestores\gestionSectoristasController@mostrarSectores');
        // MOSTRAR LOS CLIENTES DE UN SECTOR
        Route::middleware('auth:api')->get('sectorista/gestion/sector/{sectorId}', 'Apis\Gestores\gestionSectoristasController@mostrarClientesSector');
        // MOSTRAR TODOS LOS CLIENTES DE ESE SECTOR
        Route::middleware('auth:api')->get('sectorista/gestion/clientesTodosExcepcion/{sectorId}', 'Apis\Gestores\gestionSectoristasController@mostrarClientesTODO');

// GESTION FREE
Route::middleware('auth:api')
        ->get('gestionFree/clientesTodos/{sectoristaId}', 'Apis\Gestores\gestionFreeController@mostrarClientes');

Route::middleware('auth:api')
        ->get('gestionFree/clientesTodosExcepcion/{sectoristaId}', 'Apis\Gestores\gestionFreeController@mostrarClientesTODO');

Route::middleware('auth:api')
        ->get('gestionFree/documentosTodos/{sectoristaId}', 'Apis\Gestores\gestionFreeController@todosDocumentos');

// VENCIMIENTOS
Route::middleware('auth:api')->get('vencimiento/empresa/fechas/{id}', 'Apis\VencimientosController@mostrarFechasEmpresa');
Route::middleware('auth:api')->get('vencimiento/socio/fechas/{id}', 'Apis\VencimientosController@mostrarFechasSocio');
Route::middleware('auth:api')->get('vencimiento/sectorista/fechas/{id}', 'Apis\VencimientosController@mostrarFechasSectorista');
Route::middleware('auth:api')->get('vencimiento/gestor/fechas/{id}', 'Apis\VencimientosController@mostrarFechasGestor');
Route::middleware('auth:api')->get('vencimiento/free/fechas/{id}', 'Apis\Vencimientos\vencmientosFreeController@mostrarFechasSectoristasFree');
Route::middleware('auth:api')->get('vencimiento/admin/fechas/{id}', 'Apis\VencimientosController@');

Route::middleware('auth:api')
        ->get('vencimiento/empresa/fechasEspecifica/{id}', 'Apis\VencimientosController@fechasDocumentosEmpresa');
Route::middleware('auth:api')
        ->get('vencimiento/socio/fechasEspecifica/{id}', 'Apis\VencimientosController@fechasDocumentosSocio');
Route::middleware('auth:api')
        ->get('vencimiento/sectorista/fechasEspecifica/{id}', 'Apis\VencimientosController@fechasDocumentosSectorista');
Route::middleware('auth:api')
        ->get('vencimiento/gestor/fechasEspecifica/{id}', 'Apis\VencimientosController@fechasDocumentosGestor');
Route::middleware('auth:api')
        ->get('vencimiento/free/fechasEspecifica/{id}', 'Apis\Vencimientos\vencmientosFreeController@documentosSectoristasFree');
Route::middleware('auth:api')
        ->get('vencimiento/admin/fechasEspecifica/{id}', 'Apis\VencimientosController@fechasDocumentosEmpresa');


// ESTADISTICAS

Route::middleware('auth:api')->get('estadistica/empresasTodas', 'Apis\EstadisticaController@mostrarEmpresas');
Route::middleware('auth:api')->get('estadistica/sociosTodos/{empresaid}', 'Apis\EstadisticaController@mostrarSocios');
Route::middleware('auth:api')->get('estadistica/clientesTodos/{socioid}', 'Apis\EstadisticaController@mostrarClientes');
Route::middleware('auth:api')->get('estadistica/cliente/{clienteid}', 'Apis\EstadisticaController@mostrarCliente');

// FILTRO MAYOR ESTADISTICAS
        Route::middleware('auth:api')->get('estadistica/empresasTodas/filtroMayor', 'Apis\EstadisticaController@filtroMayorAdm');
        Route::middleware('auth:api')->get('estadistica/filtroMayor/empresas/{idEmpresa}', 'Apis\EstadisticaController@filtroMayorEmp');
        Route::middleware('auth:api')->get('estadistica/filtroMayor/socio/{idSocio}', 'Apis\EstadisticaController@filtroMayorSoc');
        Route::middleware('auth:api')->get('estadistica/filtroMayor/sectorista/{idSectorista}', 'Apis\EstadisticaController@filtroMayorSec');
        Route::middleware('auth:api')->get('estadistica/filtroMayor/sectorista/sector/{idSector}', 'Apis\EstadisticaController@filtroMayorSecSector');


// ESTADISTICAS DE UN GESTOR
Route::middleware('auth:api')
        ->get('estadistica/gestor/clientesTodos/{gestorId}', 'Apis\Estadisticas\gestorEmpresasController@mostrarClientes');

// ESTADISTICAS DE UN SECTORISTA
Route::middleware('auth:api')
        ->get('estadistica/sectorista/sectores/{sectoristaId}', 'Apis\Estadisticas\estadisticasSectoristasController@mostrarSectores');

Route::middleware('auth:api')->get('estadistica/sectorista/sector/{sectorId}', 'Apis\Estadisticas\estadisticasSectoristasController@mostrarClientesTODO');

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
Route::middleware('auth:api')->post('dato/editarPago', 'Apis\DatoController@editarPago');
Route::middleware('auth:api')->post('dato/editarDocumento', 'Apis\DatoController@editarDocumento');
Route::middleware('auth:api')->post('dato/eliminarPago', 'Apis\DatoController@eliminarPago');

Route::middleware('auth:api')->post('dato/eliminarDocumento', 'Apis\DatoController@eliminarDocumento');

Route::middleware('auth:api')->get('dato/clientesTodos/{socioid}', 'Apis\DatoController@mostrarClientes');
Route::middleware('auth:api')->get('dato/clienteDocumentos/{clienteid}', 'Apis\DatoController@mostrarClienteDocumentos');
Route::middleware('auth:api')->get('dato/clienteDocumentoPagos/{clienteid}', 'Apis\DatoController@mostrarClienteDocumentoPagos');

Route::middleware('auth:api')
        ->get('dato/clienteDatos/{clienteId}/{socioId}', 'Apis\DatoController@clienteDatos');

Route::middleware('auth:api')->post('dato/eliminarDireccion', 'Apis\DatoController@eliminarDireccion');
Route::middleware('auth:api')->post('dato/eliminarTelefono', 'Apis\DatoController@eliminarTelefonos');
Route::middleware('auth:api')->post('dato/eliminarCorreos', 'Apis\DatoController@eliminarCorreos');

// DATOS FREE
Route::middleware('auth:api')
        ->get('datoFree/clientesTodos/{sectoristaId}', 'Apis\Datos\datosFreeController@mostrarClientes');

Route::middleware('auth:api')
        ->post('datoFree/buscarClientes', 'Apis\Datos\datosFreeController@buscarClientes');

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
Route::middleware('auth:api')->get('usuario/socio/sectores/{socioid}', 'Apis\UsuarioController@mostrarSectoresSocio');

// Eliminar sector a un usuario

Route::middleware('auth:api')->post('usuario/sectorista/revocarSector', 'Apis\UsuarioController@revocarSectorSectorista');
Route::middleware('auth:api')->post('usuario/socio/eliminarSector', 'Apis\UsuarioController@eliminarSectorSocio');

// Editar sector a un usuario
Route::middleware('auth:api')->post('usuario/sectorista/editarSector', 'Apis\UsuarioController@editarSectorSectorista');
Route::middleware('auth:api')->post('usuario/gestor/editarSector', 'Apis\UsuarioController@editarSectorGestor');

// Añadir un sector a un usuario
Route::middleware('auth:api')->post('usuario/sectorista/anadirSector', 'Apis\UsuarioController@anadirSectorSectorista');

// Editar usuario
Route::middleware('auth:api')->post('usuario/sectorista/editarUsuario', 'Apis\UsuarioController@editarSectorista');
Route::middleware('auth:api')->post('usuario/gestor/editarUsuario', 'Apis\UsuarioController@editarGestor');


Route::middleware('auth:api')->get('usuario/sectorista/sector/{sectoristaid}/{socioid}', 'Apis\UsuarioController@mostrarSectoresSectorista');

Route::middleware('auth:api')->get('usuario/datoSocioEmpresaSectores/{socioid}', 'Apis\UsuarioController@mostrarSocioEmpresaSectores');
Route::middleware('auth:api')->get('usuario/datoSocioEmpresaSectores/gestor/{socioid}/{gestorId}', 'Apis\UsuarioController@mostrarSocioEmpresaSectorGestor');
Route::middleware('auth:api')->get('usuario/datoSocioEmpresaSectores/sectorista/{socioid}/{sectoristaId}', 'Apis\UsuarioController@mostrarSocioEmpresaSectoresSectorista');
Route::middleware('auth:api')->get('usuario/degradar/gestor/{socioid}/{gestorId}', 'Apis\UsuarioController@degradarGestor');
Route::middleware('auth:api')->get('usuario/degradar/sectorista/{socioid}/{sectoristaId}', 'Apis\UsuarioController@degradarSectorista');

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


