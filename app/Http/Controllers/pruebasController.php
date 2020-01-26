<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\direcciones;
use App\telefonos;
use App\correos;

class pruebasController extends Controller
{
    public function duplicados()
    {
        $direcciones = direcciones::all();


        foreach($direcciones as $direccion)
        {
            foreach($direcciones as $nuevaDireccion){
                if($direccion->calle == $nuevaDireccion->calle && $nuevaDireccion->cliente_id == $direccion->cliente_id  ){
                    $direcionId = $direccion->id;
                    $direccionEliminar = direcciones::find($direcionId);
                    if($direccionEliminar->delete()){
                        echo "DIRECCION ELIMINADA ".$direcionId.'<br>';
                        break;
                    }else{
                        return "ERROR ".$direcionId.'<br>';
                    }

                }
            }
            

        }
    }

    public function duplicadosTelefonos()
    {
        $telefonos = telefonos::all();


        foreach($telefonos as $telefono)
        {
            foreach($telefonos as $nuevoTelefono){
                if($telefono->numero == $nuevoTelefono->numero && $nuevoTelefono->cliente_id == $telefono->cliente_id  ){
                    $telefonoId = $telefono->id;
                    $telefonoEliminar = telefonos::find($telefonoId);

                    if($telefonoEliminar->delete()){
                        echo "TELEFONO ELIMINADA ".$telefonoId.'<br>';
                        break;
                    }else{
                        return "ERROR ".$telefonoId.'<br>';
                    }

                }
            }
            

        }
    }

    public function duplicadosCorreos()
    {
        $correos = correos::all();


        foreach($correos as $correo)
        {
            foreach($correos as $nuevoCorreo){
                if($correo->correo == $nuevoCorreo->correo && $nuevoCorreo->cliente_id == $correo->cliente_id  ){
                    $correoId = $correo->id;
                    $correoEliminar = correos::find($correoId);
                    if($correoEliminar->delete()){
                        echo "CORREO ELIMINADA ".$correoId.'<br>';
                        break;
                    }else{
                        return "ERROR ".$correoId.'<br>';
                    }

                }
            }
            

        }
    }
}
