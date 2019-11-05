<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/prueba', 'mensajesController@mensaje')
        ->name('mensaje');

Route::get('/confirmar/{token}', 'mensajesController@confirmar')
        ->name('confirmar');

Route::get('/recuperar/email/{email}', 'Apis\Auth\recuperarContrasenaController@enviarMensajeRecuperacion')
        ->name('recuperar.mensaje');

Route::get('/recuperar/{token}', 'Apis\Auth\recuperarContrasenaController@recuperarContrasena')
        ->name('recuperar');

Route::post('/recuperar/contrasena', 'Apis\Auth\recuperarContrasenaController@cambiarContrasena')
        ->name('recuperar.contrasena');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');




Route::get('/ejemplos', 'importarSocioController@ejemplosImportar')
        ->name('importar.ejemplos');

Route::get('/ejemplos/mostrar/{nombreExcel}', 'importarSocioController@ejemplosimportarmostrar')
        ->name('importar.mostrar');

Route::get('/ejemplos/descargar/{nombreExcel}', 'importarSocioController@ejemplosimportardescargar')
        ->name('importar.descargar');
 
// RUTAS DE PREVISUALICION EN CADA CASO PARA SOCIOS
Route::post('/importar/mostrar/clientes', 'web\mostrar\mostrarSocioController@clientes')
        ->name('importar.mostrar.clientes'); 

Route::post('/importar/mostrar/correos', 'web\mostrar\mostrarSocioController@correos')
        ->name('importar.mostrar.correos'); 

Route::post('/importar/mostrar/telefonos', 'web\mostrar\mostrarSocioController@telefonos')
        ->name('importar.mostrar.telefonos'); 

Route::post('/importar/mostrar/direcciones', 'web\mostrar\mostrarSocioController@direcciones')
        ->name('importar.mostrar.direcciones');

Route::post('/importar/mostrar/documentos', 'web\mostrar\mostrarSocioController@documentos')
        ->name('importar.mostrar.documentos');

Route::post('/importar/mostrar/pagos', 'web\mostrar\mostrarSocioController@pagos')
        ->name('importar.mostrar.pagos');


Route::get('/importar/datos', 'importarSocioController@datos')
        ->name('importar.datos'); 

Route::post('/importar/mostrarExcel', 'importarSocioController@mostrarExcel')
        ->name('importar.mostrarExcel');

Route::post('/importar/clientes', 'importarSocioController@importarClientes')
        ->name('importar.importarClientes');

Route::post('/importar/correos', 'importarSocioController@importarCorreos')
        ->name('importar.importarCorreos');

Route::post('/importar/telefonos', 'importarSocioController@importarTelefonos')
        ->name('importar.importarTelefonos');

Route::post('/importar/direcciones', 'importarSocioController@importarDirecciones')
        ->name('importar.importarDirecciones');

Route::post('/importar/documentos', 'importarSocioController@importarDocumentos')
        ->name('importar.importarDocumentos');

Route::post('/importar/pagos', 'importarSocioController@importarPagos')
        ->name('importar.importarPagos');


Route::get('/dni', 'Apis\RegisterController@dni');