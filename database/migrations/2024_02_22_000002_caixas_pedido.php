<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('caixas_pedidos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pedidos_id')->unsigned();
            $table->integer('a')->unsigned();
            $table->integer('l')->unsigned();
            $table->integer('c')->unsigned();
            $table->integer('peso')->unsigned();
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
        Schema::dropIfExists('caixas_pedidos');
    }
};
