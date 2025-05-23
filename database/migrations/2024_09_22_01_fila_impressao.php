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
        Schema::create('fila_impressao', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('estoque_id')->unsigned()->index();
            $table->date('data_impresso');
            $table->integer('impresso');
            $table->timestamps();
        });
    }


/**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fila_impressao');
    }
};
