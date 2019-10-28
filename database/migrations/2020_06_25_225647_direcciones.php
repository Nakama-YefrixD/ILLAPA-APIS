<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Direcciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('direcciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            // $table->unsignedBigInteger('socio_id');
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('correo_id');
            $table->string('calle', 500);
            $table->string('ciudad', 30)->nullable();
            $table->integer('codigopostal')->nullable();
            $table->string('pais')->nullable();
            $table->string('latitud')->nullable();
            $table->string('longitud')->nullable();
            $table->integer('estado');
            $table->timestamps();

            // $table->foreign('socio_id')->references('id')->on('socios');
            $table->foreign('cliente_id')->references('id')->on('clientes');
            $table->foreign('correo_id')->references('id')->on('users');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('direcciones');
    }
}
