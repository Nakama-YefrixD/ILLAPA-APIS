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
            'estado' => 1,
        ]);
    }
}
