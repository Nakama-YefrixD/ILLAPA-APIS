<?php

use Illuminate\Database\Seeder;
use App\empresas;

class empresasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        empresas::create([
            'correo_id' => 1,
            'nombre' => 'ILLAPA',
            'estado' => 1,

        ]);
        // empresas::create([
        //     'correo_id' => 2,
        //     'nombre' => 'B&B',
        //     'estado' => 1,

        // ]);
    }
}
