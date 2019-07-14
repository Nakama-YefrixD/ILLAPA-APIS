<?php

use Illuminate\Database\Seeder;
use App\gestores;

class gestoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        gestores::create([
            'sector_id' => 1,
            'correo_id' => 5,
            'estado' => 1,
        ]);
        
        gestores::create([
            'sector_id' => 2,
            'correo_id' => 6,
            'estado' => 1,
        ]);

        gestores::create([
            'sector_id' => 3,
            'correo_id' => 7,
            'estado' => 1,
        ]);

        
    }
}
