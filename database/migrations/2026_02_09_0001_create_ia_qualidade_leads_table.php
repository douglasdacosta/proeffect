<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ia_qualidade_leads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pedido_id')->unique();
            $table->dateTime('data_entrega')->nullable();
            $table->string('os')->nullable();
            $table->string('ep')->nullable();
            $table->integer('qtde')->nullable();
            $table->unsignedBigInteger('pessoas_id')->nullable();
            $table->string('contato_pos_venda')->nullable();
            $table->string('numero_whatsapp_pos_venda')->nullable();
            $table->string('responsavel_qualidade')->nullable();
            $table->string('status_lead', 20)->default('pendente');
            $table->dateTime('datahora_envio_ultimo_lead')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ia_qualidade_leads');
    }
};
