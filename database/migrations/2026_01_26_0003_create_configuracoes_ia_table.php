<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('configuracoes_ia', function (Blueprint $table) {
            $table->id();
            $table->integer('tempo_entrega_dias')->default(30); // Tempo de entrega (em dias)
            $table->integer('tempo_cliente_sem_compra_dias')->default(30); // Tempo que o cliente não compra (em dias)
            $table->string('status', 1)->default('A');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('configuracoes_ia');
    }
};
