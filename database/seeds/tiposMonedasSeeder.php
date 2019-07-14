<?php

use Illuminate\Database\Seeder;
use App\tiposMonedas;

class tiposMonedasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        tiposMonedas::create([
            'id' => 1,
            'nombre' => 'PEN',
            'estado' => 1,
        ]);

        tiposMonedas::create([
            'id' => 2,
            'nombre' => 'USD',
            'estado' => 1,
        ]);

        tiposMonedas::create([
            'id' => 3,
            'nombre' => 'EUR',
            'estado' => 1,
        ]);
    }
}
