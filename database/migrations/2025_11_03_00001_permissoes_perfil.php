acoes<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('permissoes_perfis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('perfil_id');
            $table->unsignedBigInteger('acao_id');
            $table->unsignedBigInteger('submenus_id');
            $table->boolean('permitido')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('agendamentos');
    }
};
