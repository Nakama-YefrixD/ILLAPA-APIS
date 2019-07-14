<?php

use Illuminate\Database\Seeder;
use App\telefonos;

class telefonosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        telefonos::create([
            'correo_id' => 1,
            'prefijo' => '+51',
            'numero' => 987140650,
            'tipo' => 1,
            'estado' => 1,

        ]);

        telefonos::create([
            'correo_id' => 2,
            'prefijo' => '+51',
            'numero' => 987140650,
            'tipo' => 1,
            'estado' => 1,

        ]);

        telefonos::create([
            'correo_id' => 3,
            'prefijo' => '+51',
            'numero' => 425156,
            'tipo' => 2,
            'estado' => 1,

        ]);

        telefonos::create([
            'correo_id' => 4,
            'prefijo' => '+51',
            'numero' => 987140650,
            'tipo' => 1,
            'estado' => 0,

        ]);
    }
}
