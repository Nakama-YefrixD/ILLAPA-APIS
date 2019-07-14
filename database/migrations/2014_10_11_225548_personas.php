<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Personas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->bigIncrements('id');
            // $table->integer('tipoidentificacion')->nullable();
            $table->unsignedBigInteger('tipoDocumentoIdentidad_id');
            $table->integer('numeroidentificacion')->nullable();
            $table->string('nombre')->nullable();
            $table->string('imagen', 100)->default('https://cdn.pixabay.com/photo/2015/03/04/22/35/head-659651_960_720.png');
            $table->integer('estado')->nullable();
            $table->timestamps();

            $table->foreign('tipoDocumentoIdentidad_id')->references('id')->on('tiposDocumentosIdentidad');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personas');
    }
}