<?php

use Illuminate\Database\Seeder;
use App\personas;


class personasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        personas::create([
            'tipoDocumentoIdentidad_id' => 1,
            'numeroidentificacion' => 12345678,
            'nombre' => 'Raul Pozo',
            // 'imagen' => ,
            'estado' => 1,
        ]);

        // personas::create([
        //     'tipoidentificacion' => 2,
        //     'numeroidentificacion' => 12345678,
        //     'nombre' => 'Heber Rodriguez',
        //     'imagen' => 'imagen',
        //     'estado' => 1,
        // ]);

        // personas::create([
        //     'tipoidentificacion' => 1,
        //     'numeroidentificacion' => 73819623,
        //     'nombre' => 'persona C',
        //     'imagen' => 'imagen',
        //     'estado' => 1,
        // ]);
        
        // personas::create([
        //     'tipoidentificacion' => 1,
        //     'numeroidentificacion' => 73123651,
        //     'nombre' => 'persona D',
        //     'imagen' => 'imagen',
        //     'estado' => 0,
        // ]);



        // personas::create([
        //     'tipoidentificacion' => 1,
        //     'numeroidentificacion' => 85296314,
        //     'nombre' => 'persona E',
        //     'imagen' => 'imagen',
        //     'estado' => 0,
        // ]);
        // personas::create([
        //     'tipoidentificacion' => 1,
        //     'numeroidentificacion' => 19495963,
        //     'nombre' => 'persona F',
        //     'imagen' => 'imagen',
        //     'estado' => 0,
        // ]);
        // personas::create([
        //     'tipoidentificacion' => 1,
        //     'numeroidentificacion' => 28282828,
        //     'nombre' => 'persona G',
        //     'imagen' => 'imagen',
        //     'estado' => 0,
        // ]);
        // personas::create([
        //     'tipoidentificacion' => 1,
        //     'numeroidentificacion' => 23635215,
        //     'nombre' => 'persona H',
        //     'imagen' => 'imagen',
        //     'estado' => 0,
        // ]);
    }
    
}




