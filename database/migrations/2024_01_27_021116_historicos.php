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
        Schema::create('historicos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pedidos_id')->unsigned();
            $table->text('historico')->nullable();
            $table->string('status',1);
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
        Schema::dropIfExists('historicos');
    }
};
