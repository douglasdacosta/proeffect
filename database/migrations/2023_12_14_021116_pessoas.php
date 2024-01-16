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
        Schema::create('pessoas', function (Blueprint $table) {
            $table->id();
            $table->string('nome',100);
            $table->string('tipo_pessoa');
            $table->string('documento',20);
            $table->string('endereco', 200);
            $table->string('numero', 10);
            $table->string('cep', 9);
            $table->string('bairro', 150);
            $table->string('cidade', 150);
            $table->string('estado', 150);
            $table->string('telefone1', 11);
            $table->string('telefone2', 11);
            $table->string('telefone3', 11);
            $table->date('data_cadastro');
            $table->longText('observacoes');            
            $table->boolean('status');
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
        //
    }
};
