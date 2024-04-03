<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('descricao_permissoes', function (Blueprint $table) {
            $table->id();
            $table->string('nome',100);
            $table->timestamps();
        });

        DB::table('descricao_permissoes')->insert(
            [
                ['nome' => 'Visualisar'],
                ['nome' => 'Incluir'],
                ['nome' => 'Alterar'],
                ['nome' => 'Inativar'],
                ['nome' => 'Deletar'],
                ['nome' => 'Enviar'],
            ]
            );

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('descricao_permissoes');
    }
};
