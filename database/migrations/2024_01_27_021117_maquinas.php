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
        Schema::create('maquinas', function (Blueprint $table) {
            $table->id();
            $table->integer('qtde_maquinas')->length(11);
            $table->time('horas_maquinas')->length(11);
            $table->integer('pessoas_acabamento')->length(11);
            $table->integer('pessoas_montagem')->length(11);
            $table->integer('pessoas_inspecao')->length(11);
            $table->time('horas_dia')->length(11);
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
        Schema::dropIfExists('maquinas');
    }
};
