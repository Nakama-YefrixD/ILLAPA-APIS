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




Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');




Route::get('/ejemplos', 'importarController@ejemplosImportar')
        ->name('importar.ejemplos');

Route::get('/ejemplos/mostrar/{nombreExcel}', 'importarController@ejemplosimportarmostrar')
        ->name('importar.mostrar');

Route::get('/ejemplos/descargar/{nombreExcel}', 'importarController@ejemplosimportardescargar')
        ->name('importar.descargar');
 
Route::get('/importar/datos', 'importarController@datos')
        ->name('importar.datos'); 

Route::post('/importar/mostrarExcel', 'importarController@mostrarExcel')
        ->name('importar.mostrarExcel');

Route::post('/importar/clientes', 'importarController@importarClientes')
        ->name('importar.importarClientes');

Route::post('/importar/correos', 'importarController@importarCorreos')
        ->name('importar.importarCorreos');

Route::post('/importar/telefonos', 'importarController@importarTelefonos')
        ->name('importar.importarTelefonos');

Route::post('/importar/direcciones', 'importarController@importarDirecciones')
        ->name('importar.importarDirecciones');

Route::post('/importar/documentos', 'importarController@importarDocumentos')
        ->name('importar.importarDocumentos');

Route::post('/importar/pagos', 'importarController@importarPagos')
        ->name('importar.importarPagos');