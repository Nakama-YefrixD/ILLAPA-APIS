<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Clientes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->bigIncrements('id');
            // $table->unsignedInteger('socio_id');
            $table->unsignedBigInteger('sector_id');
            $table->unsignedBigInteger('correo_id');
            $table->string('imagen', 100)->default('https://cdn.pixabay.com/photo/2015/03/04/22/35/head-659651_960_720.png');
            $table->integer('estado');
            $table->timestamps();

            $table->foreign('sector_id')->references('id')->on('sectores');
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
        Schema::dropIfExists('clientes');
    }
}
