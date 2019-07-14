<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Sectores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sectores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('socio_id');
            $table->unsignedBigInteger('sectorista_id')->nullable();
            $table->text('descripcion');
            $table->integer('estado');
            $table->integer('estSectorista')->nullable();
            $table->integer('estGestor')->nullable();
            $table->timestamps();

            $table->foreign('socio_id')->references('id')->on('socios');
            $table->foreign('sectorista_id')->references('id')->on('sectoristas');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */


    public function down()
    {
        Schema::dropIfExists('sectores');
    }
}
