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
        Schema::create('producao_maquinas', function (Blueprint $table) {
            $table->id();
            $table->integer('numero_cnc')->length(11);
            $table->float('HorasServico', 11,3);
            $table->integer('metrosPercorridos')->length(11);;
            $table->text('qtdeServico');
            $table->date('data');
            $table->time('hora');
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
        Schema::dropIfExists('producao_maquinas');
    }
};
