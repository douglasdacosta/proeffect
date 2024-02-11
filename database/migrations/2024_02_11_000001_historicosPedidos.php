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
        Schema::create('historicos_pedidos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pedidos_id')->unsigned();
            $table->bigInteger('status_id')->unsigned();
            $table->timestamps();
            $table->foreign('pedidos_id')->references('id')->on('pedidos');
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
