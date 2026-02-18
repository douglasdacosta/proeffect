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
        Schema::create('renovacoes', function (Blueprint $table) {
            $table->id();
            $table->dateTime('data_abertura')->nullable();
            $table->unsignedBigInteger('departamento_id')->index();
            $table->text('descricao')->nullable();
            $table->string('responsavel')->nullable();
            $table->string('numero_documento')->nullable();
            $table->string('periodo_renovacao')->nullable();
            $table->dateTime('data_vencimento')->nullable();
            $table->dateTime('inicio_renovacao')->nullable();
            $table->integer('previsao')->nullable();
            $table->dateTime('data_finalizado')->nullable();
            $table->string('status', 1)->default('P');
            $table->timestamps();

            $table->foreign('departamento_id')->references('id')->on('perfis');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('renovacoes');
    }
};
