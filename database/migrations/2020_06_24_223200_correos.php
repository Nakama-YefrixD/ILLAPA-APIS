<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Correos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('correos', function (Blueprint $table) {
            $table->bigIncrements('id');
            // $table->unsignedBigInteger('socio_id');
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('correo_id');
            $table->string('correo');
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
        Schema::dropIfExists('correos');
    }
}
