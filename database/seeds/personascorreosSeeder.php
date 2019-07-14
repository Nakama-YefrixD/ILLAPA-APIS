<?php

use Illuminate\Database\Seeder;
use App\personas_correos;

class personascorreosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        personas_correos::create([
            'persona_id' => 1,
            'correo' => 'peronaA@hotmail.com',
            'estado' => 1

        ]);

        personas_correos::create([
            'persona_id' => 2,
            'correo' => 'peronaB@hotmail.com',
            'estado' => 1

        ]);

        personas_correos::create([
            'persona_id' => 3,
            'correo' => 'peronaC@hotmail.com',
            'estado' => 1

        ]);
        personas_correos::create([
            'persona_id' => 4,
            'correo' => 'peronaD@hotmail.com',
            'estado' => 0

        ]);
    }
}
