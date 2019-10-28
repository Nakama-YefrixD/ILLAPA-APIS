<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Pagos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('documento_id');
            $table->unsignedBigInteger('tipoPago_id')->nullable();
            $table->string('numero', 15);
            $table->date('fechaemision');
            $table->date('fechavencimiento');
            $table->string('moneda', 3);
            $table->decimal('importe', 10,2);
            $table->decimal('saldo', 10,2);
            $table->integer('estado');
            $table->timestamps();

            $table->foreign('tipoPago_id')->references('id')->on('tiposPagos');
            $table->foreign('documento_id')->references('id')->on('documentos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pagos');
    }
}
