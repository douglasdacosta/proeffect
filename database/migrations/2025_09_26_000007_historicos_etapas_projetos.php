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
        Schema::create('historicos_etapas_projetos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('projetos_id')->unsigned();
            $table->bigInteger('status_projetos_id')->unsigned();
            $table->bigInteger('sub_status_projetos_id')->unsigned();
            $table->bigInteger('funcionarios_id')->unsigned();
            $table->bigInteger('etapas_pedidos_id')->nullable();
            $table->timestamps();
            $table->foreign('projetos_id')->references('id')->on('projetos');
            $table->foreign('status_projetos_id')->references('id')->on('status_projetos');
            $table->foreign('sub_status_projetos_id')->references('id')->on('sub_status_projetos');
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
        Schema::dropIfExists('historicos_etapas_projetos');
    }
};
