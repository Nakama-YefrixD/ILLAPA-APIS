<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Contratos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('socio_id');
            $table->unsignedBigInteger('plan_id');
            $table->date('fechainicio');
            $table->date('fechaexpiracion');
            $table->integer('estado');
            $table->timestamps();

            $table->foreign('socio_id')->references('id')->on('socios');
            $table->foreign('plan_id')->references('id')->on('planes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contratos');
    }
}
