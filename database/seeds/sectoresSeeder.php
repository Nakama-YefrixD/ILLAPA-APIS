<?php

use Illuminate\Database\Seeder;
use App\sectores;

class sectoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        sectores::create([
            'socio_id' => 2,
            'sectorista_id' => 1,
            'descripcion' => 'sector 1 de la empresa A del socio 1',
            'estado' => 1,
            'estSectorista' => 1,
            'estGestor' => 1,
        ]);

        sectores::create([
            'socio_id' => 2,
            'sectorista_id' => 1,
            'descripcion' => 'sector 2 de la empresa A del socio 1',
            'estado' => 1,
            'estSectorista' => 1,
            'estGestor' => 1,
        ]);

        sectores::create([
            'socio_id' => 2,
            'sectorista_id' => 1,
            'descripcion' => 'sector 3 de la empresa A del socio 1',
            'estado' => 1,
            'estSectorista' => 1,
            'estGestor' => 1,
        ]);
    }
}
