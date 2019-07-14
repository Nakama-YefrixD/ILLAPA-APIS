<?php

use Illuminate\Database\Seeder;
use App\tiposPagos;

class tiposPagosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        tiposPagos::create([
            'id' => 1,
            'nombre' => 'EFECTIVO',
            'estado' => 1,
        ]);

        tiposPagos::create([
            'id' => 2,
            'nombre' => 'CHEQUE',
            'estado' => 1,
        ]);

        tiposPagos::create([
            'id' => 3,
            'nombre' => 'DEPOSITO',
            'estado' => 1,
        ]);

        tiposPagos::create([
            'id' => 4,
            'nombre' => 'TRANSFERENCIA',
            'estado' => 1,
        ]);

    }
}
