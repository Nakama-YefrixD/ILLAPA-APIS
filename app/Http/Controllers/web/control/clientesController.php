<?php

namespace App\Http\Controllers\web\control;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\clientes;

class clientesController extends Controller
{
    public function index()
    {
        $clientes = clientes::all();
        $data = array(
            'clientes' => $clientes,
        );

        return view('web.control.clientes.index')->with($data);
    }

    public function tb_clientes()
    {

    }


}
