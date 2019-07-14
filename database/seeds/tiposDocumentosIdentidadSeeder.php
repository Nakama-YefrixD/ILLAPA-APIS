<?php

use Illuminate\Database\Seeder;
use App\tiposDocumentosIdentidad;

class tiposDocumentosIdentidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        tiposDocumentosIdentidad::create([
            'id' => 1,
            'nombre' => 'DNI',
            'estado' => 1,
        ]);

        tiposDocumentosIdentidad::create([
            'id' => 2,
            'nombre' => 'RUC',
            'estado' => 1,
        ]);

        tiposDocumentosIdentidad::create([
            'id' => 3,
            'nombre' => 'CI',
            'estado' => 1,
        ]);

    }
}
