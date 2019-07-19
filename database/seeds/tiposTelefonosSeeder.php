<?php

use Illuminate\Database\Seeder;
use App\tiposTelefonos;

class tiposTelefonosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        tiposTelefonos::create([
            'id' => 1,
            'nombre' => 'CASA',
            'estado' => 1,
        ]);

        tiposTelefonos::create([
            'id' => 2,
            'nombre' => 'EMPRESA',
            'estado' => 1,
        ]);
    }
}
