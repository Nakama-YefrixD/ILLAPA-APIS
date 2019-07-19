<?php

use Illuminate\Database\Seeder;
use App\clientes;

class clientesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        clientes::create([
            'sector_id' => 1,
            'correo_id' => 8,
            'imagen' => "https://cdn.pixabay.com/photo/2015/03/04/22/35/head-659651_960_720.png",
            'estado' => 1,
        ]);
    }
}
