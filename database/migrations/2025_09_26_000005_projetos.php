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
        Schema::create('projetos', function (Blueprint $table) {
            $table->id();
            $table->integer('os')->length(11);
            $table->string('ep',100);
            $table->bigInteger('pessoas_id')->unsigned()->index();
            $table->bigInteger('funcionarios_id')->unsigned()->nullable();
            $table->bigInteger('status_projetos_id')->nullable();
            $table->bigInteger('sub_status_projetos_codigo')->nullable();
            $table->bigInteger('transporte_id')->unsigned()->nullable();
            $table->bigInteger('prioridade_id')->unsigned()->index()->nullable();
            $table->bigInteger('etapa_projeto_id')->nullable();
            $table->integer('qtde')->length(11);
            $table->dateTime('data_gerado');
            $table->dateTime('data_entrega')->nullable();
            $table->integer('cliente_ativo')->length(11)->nullable();
            $table->integer('novo_alteracao')->length(11)->nullable();
            $table->integer('com_pedido')->length(11)->nullable();
            $table->integer('em_alerta')->length(11)->nullable();
            $table->time('tempo_projetos')->nullable();
            $table->time('tempo_programacao')->nullable();
            $table->float('valor_unitario_adv',11, 2)->nullable();
            $table->text('observacao')->nullable();
            $table->dateTime('data_status')->nullable();
            $table->dateTime('data_tarefa')->nullable();
            $table->string('status',1);
            $table->timestamps();

            $table->foreign('sub_status_projetos_codigo')->references('codigo')->on('sub_status_projetos');
            $table->foreign('pessoas_id')->references('id')->on('pessoas');
            $table->foreign('transporte_id')->references('id')->on('transportes');
            $table->foreign('prioridade_id')->references('id')->on('prioridades');
            $table->foreign('funcionarios_id')->references('id')->on('funcionarios');

        });



    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projetos');
    }
};
