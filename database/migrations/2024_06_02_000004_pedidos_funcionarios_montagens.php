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
        Schema::create('pedidos_funcionarios_montagens', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pedido_id')->unsigned()->index();
            $table->bigInteger('funcionario_id')->unsigned()->index();
            $table->timestamps();

            $table->foreign('pedido_id')->references('id')->on('pedidos');
            $table->foreign('funcionario_id')->references('id')->on('funcionarios');

        });

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedidos_funcionarios_montagens');
    }
};
