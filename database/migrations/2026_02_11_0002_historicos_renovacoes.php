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
        Schema::create('historicos_renovacoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('renovacoes_id')->index();
            $table->text('historico');
            $table->string('status', 1)->default('A');
            $table->timestamps();

            $table->foreign('renovacoes_id')->references('id')->on('renovacoes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historicos_renovacoes');
    }
};
