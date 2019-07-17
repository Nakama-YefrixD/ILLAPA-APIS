<?php

use Illuminate\Database\Seeder;
use App\tramos;

class tramosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        tramos::create([
            'id' => 1,
            'socio_id' => 1,
            'nombre' => 'VIGENTES',
            'inicio' => -1000,
            'fin' => 0,
            'estado' => 1,

        ]);

        tramos::create([
            'id' => 2,
            'socio_id' => 1,
            'nombre' => 'VENCIDOS',
            'inicio' => 1,
            'fin' => 1000,
            'estado' => 1,

        ]);
    }
}
