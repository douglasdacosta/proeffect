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
            $table->string('documento',20)->nullable();
            $table->string('endereco', 200)->nullable();
            $table->string('numero', 10)->nullable();
            $table->string('cep', 9)->nullable();
            $table->string('bairro', 150)->nullable();
            $table->string('cidade', 150)->nullable();
            $table->string('estado', 150)->nullable();
            $table->string('telefone', 11)->nullable();
            $table->string('email', 100)->nullable();                 
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
