<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(tiposDocumentosIdentidadSeeder::class);
        $this->call(tiposDocumentosSeeder::class);
        $this->call(tiposMonedasSeeder::class);
        $this->call(tiposPagosSeeder::class);
        $this->call(tiposAccionesSeeder::class);
        
        // $this->call(UsersTableSeeder::class);
        $this->call(personasSeeder::class);
        $this->call(users::class);
        // $this->call(direccionesSeeder::class);
        // $this->call(telefonosSeeder::class);
        $this->call(empresasSeeder::class);
        $this->call(sociosSeeder::class);
        $this->call(tramosSeeder::class);
        // $this->call(sectoristasSeeder::class);
        // $this->call(sectoresSeeder::class);
        // $this->call(clientesSeeder::class);
        // $this->call(gestoresSeeder::class);
        
        
        
    }
}
