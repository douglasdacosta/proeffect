<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_status_projetos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('status_projetos_id')->unsigned()->index();
            $table->string('nome',100);
            $table->bigInteger('codigo')->unique();
            $table->string('status',1);
            $table->timestamps();
        });

        DB::table('sub_status_projetos')->insert(
            [
                [ 'codigo' => 1,  'status_projetos_id' => 4, 'nome' => 'Liberado para Projetos', 'status' => 'A'],
                [ 'codigo' => 2,  'status_projetos_id' => 5, 'nome' => 'Finalizado', 'status' => 'A'],
                [ 'codigo' => 10, 'status_projetos_id' => 3, 'nome' => 'Avaliação', 'status' => 'A'],
                [ 'codigo' => 11, 'status_projetos_id' => 3, 'nome' => 'Elaboração do Design', 'status' => 'A'],
                [ 'codigo' => 20, 'status_projetos_id' => 2, 'nome' => 'Agendar Reunião', 'status' => 'A'],
                [ 'codigo' => 21, 'status_projetos_id' => 2, 'nome' => 'Reunião Marcada', 'status' => 'A'],
                [ 'codigo' => 30, 'status_projetos_id' => 2, 'nome' => 'Solicitação', 'status' => 'A'],
                [ 'codigo' => 31, 'status_projetos_id' => 1, 'nome' => 'Aguardando Componente', 'status' => 'A'],
                [ 'codigo' => 32, 'status_projetos_id' => 1, 'nome' => 'Aguardando Informação', 'status' => 'A'],
                [ 'codigo' => 33, 'status_projetos_id' => 1, 'nome' => 'Aguardando Aprov. Estudo', 'status' => 'A'],
                [ 'codigo' => 34, 'status_projetos_id' => 1, 'nome' => 'Aguardando Visita Cliente', 'status' => 'A'],
                [ 'codigo' => 35, 'status_projetos_id' => 1, 'nome' => 'Aguardando Cliente Definir', 'status' => 'A'],
                [ 'codigo' => 36, 'status_projetos_id' => 5, 'nome' => 'Entregue', 'status' => 'A'],
                [ 'codigo' => 40, 'status_projetos_id' => 6, 'nome' => 'Projeto Parado', 'status' => 'A'],
                [ 'codigo' => 50, 'status_projetos_id' => 7, 'nome' => 'Em Preparação', 'status' => 'A'],
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
        Schema::dropIfExists('sub_status_projetos');
    }
};
