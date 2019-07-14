<?php

use Illuminate\Database\Seeder;
use App\sectoristas;

class sectoristasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        sectoristas::create([
            'socio_id' => 2,
            'correo_id' => 4,
            'estado' => 1,
        ]);
    }
}
