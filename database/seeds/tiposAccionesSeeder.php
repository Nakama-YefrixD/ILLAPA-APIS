<?php

use Illuminate\Database\Seeder;
use App\tiposAcciones;


class tiposAccionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        tiposAcciones::create([
            'id' => 1,
            'nombre' => 'Mapa',
            'estado' => 1,
        ]);

        tiposAcciones::create([
            'id' => 2,
            'nombre' => 'SMS',
            'estado' => 1,
        ]);

        tiposAcciones::create([
            'id' => 3,
            'nombre' => 'Teléfono',
            'estado' => 1,
        ]);

        tiposAcciones::create([
            'id' => 4,
            'nombre' => 'Whatsapp',
            'estado' => 1,
        ]);
    }
}
