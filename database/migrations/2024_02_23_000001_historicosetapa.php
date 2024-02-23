<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historicos_etapas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pedidos_id')->unsigned();
            $table->bigInteger('etapas_pedidos_id')->unsigned();
            $table->bigInteger('funcionarios_id')->unsigned();
            $table->timestamps();
            $table->foreign('pedidos_id')->references('id')->on('pedidos');
            $table->foreign('etapas_pedidos_id')->references('id')->on('etapas_pedidos');
            $table->foreign('funcionarios_id')->references('id')->on('funcionarios');
        });


    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historicos_pedidos');
    }
};
