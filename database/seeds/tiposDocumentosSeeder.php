<?php

use Illuminate\Database\Seeder;
use App\tiposDocumentos;

class tiposDocumentosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        tiposDocumentos::create([
            'id' => 1,
            'nombre' => 'FAC',
            'estado' => 1,
        ]);

        tiposDocumentos::create([
            'id' => 2,
            'nombre' => 'BOL',
            'estado' => 1,
        ]);

        tiposDocumentos::create([
            'id' => 3,
            'nombre' => 'LET',
            'estado' => 1,
        ]);
        
    }
}
