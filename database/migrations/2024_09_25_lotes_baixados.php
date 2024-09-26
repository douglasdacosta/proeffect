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
        Schema::create('lote_estoque_baixados', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('estoque_id')->unsigned()->index();
            $table->dateTime('data_baixa');
            $table->timestamps();
        });
    }


/**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()   {
        Schema::dropIfExists('lote_estoque_baixados');
    }
};
