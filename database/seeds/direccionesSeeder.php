<?php

use Illuminate\Database\Seeder;
use App\direcciones;

class direccionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        direcciones::create([
            'correo_id' => 1,
            'calle' => 'Mariano Melgar',
            'ciudad' => 'Arequipa',
            'codigopostal' => '051',
            'pais' => 'Peru',
            'latitud' => 'x00152-lat',
            'longitud' => 'y78516-long',
            'estado' => 1,

        ]);

        direcciones::create([
            'correo_id' => 2,
            'calle' => 'Selva Alegre',
            'ciudad' => 'Arequipa',
            'codigopostal' => '051',
            'pais' => 'Peru',
            'latitud' => 'x11852-lat',
            'longitud' => 'y99516-long',
            'estado' => 1,

        ]);

        direcciones::create([
            'correo_id' => 3,
            'calle' => 'Jose Luis Bustamante',
            'ciudad' => 'Arequipa',
            'codigopostal' => '051',
            'pais' => 'Peru',
            'latitud' => 'x22152-lat',
            'longitud' => 'y78816-long',
            'estado' => 1,

        ]);

        direcciones::create([
            'correo_id' => 4,
            'calle' => 'Porongoche',
            'ciudad' => 'Arequipa',
            'codigopostal' => '051',
            'pais' => 'Peru',
            'latitud' => 'x22152-lat',
            'longitud' => 'y78816-long',
            'estado' => 0,

        ]);
    }
}
