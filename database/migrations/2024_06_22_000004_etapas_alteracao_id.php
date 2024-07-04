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
        Schema::create('etapas_alteracao', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pedido_id')->unsigned()->index();
            $table->timestamps();

            $table->foreign('pedido_id')->references('id')->on('pedidos');
        });

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('etapas_alteracao');
    }
};
