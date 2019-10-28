<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Documentos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cliente_id');
            // $table->string('tipo', 3);
            $table->unsignedBigInteger('tipoDocumento_id')->nullable();
            $table->string('numero', 15);
            $table->date('fechaemision');
            $table->date('fechavencimiento');
            // $table->string('moneda', 3);
            $table->unsignedBigInteger('tipoMoneda_id');
            $table->decimal('importe', 10,2);
            $table->decimal('saldo', 10,2);
            $table->integer('estado');
            $table->timestamps();


            $table->foreign('tipoDocumento_id')->references('id')->on('tiposDocumentos');
            $table->foreign('cliente_id')->references('id')->on('clientes');
            $table->foreign('tipoMoneda_id')->references('id')->on('tiposMonedas');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documentos');
    }
}
