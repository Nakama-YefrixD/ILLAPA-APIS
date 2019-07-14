<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Gestores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gestores', function (Blueprint $table) {
            $table->bigIncrements('id');
            // $table->unsignedInteger('socio_id');
            $table->unsignedBigInteger('sector_id')->nullable();
            $table->unsignedBigInteger('correo_id');
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
        Schema::dropIfExists('gestores');
    }
}
