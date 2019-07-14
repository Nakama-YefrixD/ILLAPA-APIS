<?php

namespace App\Http\Controllers\Apis;
use App\Http\Controllers\Controller;
use DB;



use Illuminate\Http\Request;

class ApiController extends Controller
{
    
    public function curso()
    {
        $tarea = DB::table('personas')
                ->get();

        return $tarea;
    }
}
