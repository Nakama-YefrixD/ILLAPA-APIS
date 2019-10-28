<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Telefonos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telefonos', function (Blueprint $table) {
            $table->bigIncrements('id');
            // $table->unsignedBigInteger('socio_id');
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('correo_id');
            $table->string('pais', 45)->nullable();
            $table->string('prefijo', 45)->nullable();
            $table->bigInteger('numero');
            $table->unsignedBigInteger('tipotelefono_id');
            // $table->string('tipo')->nullable();
            $table->integer('estado');
            $table->timestamps();

            // $table->foreign('socio_id')->references('id')->on('socios');
            $table->foreign('cliente_id')->references('id')->on('clientes');
            $table->foreign('correo_id')->references('id')->on('users');
            $table->foreign('tipotelefono_id')->references('id')->on('tiposTelefonos');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('telefonos');
    }
}
