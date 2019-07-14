<?php

use Illuminate\Database\Seeder;
use App\socios;

class sociosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        socios::create([
            'empresa_id' => 1,
            'correo_id' => 1,
            'estado' => 1,

        ]);
        // socios::create([
        //     'empresa_id' => 2,
        //     'correo_id' => 3,
        //     'estado' => 1,

        // ]);
    }
}
