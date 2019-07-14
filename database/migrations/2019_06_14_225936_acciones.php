<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Acciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('tipoAccion_id');
            // $table->date('fecha');
            // $table->string('tipo', 3);

            // $table->unsignedBigInteger('documento_id')->nullable();

            $table->text('descripcion');
            $table->integer('flagcompromiso');
            $table->date('fechacompromiso')->nullable();
            $table->decimal('importecompromiso')->nullable();
            $table->integer('flagprorroga');
            $table->date('fechaprorroga')->nullable();
            $table->integer('flagalarma');
            $table->timestamp('fechahoraalarma')->nullable();
            $table->integer('estado');
            $table->timestamps();

            $table->foreign('cliente_id')->references('id')->on('clientes');
            $table->foreign('tipoAccion_id')->references('id')->on('tiposAcciones');
            // $table->foreign('documento_id')->references('id')->on('documentos');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acciones');
    }
}
