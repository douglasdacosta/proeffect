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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->integer('os')->length(11);

            $table->bigInteger('fichatecnica_id')->unsigned()->index();
            $table->bigInteger('pessoas_id')->unsigned()->index();
            $table->bigInteger('status_id')->unsigned()->index();
            $table->bigInteger('transporte_id')->unsigned()->index();
            $table->bigInteger('prioridade_id')->unsigned()->index();
            $table->integer('qtde')->length(11);
            $table->dateTime('data_gerado');
            $table->dateTime('data_entrega');
            $table->text('observacao')->length(11)->nullable();
            $table->string('status',1);
            $table->timestamps();

            $table->foreign('fichatecnica_id')->references('id')->on('ficha_tecnica');
            $table->foreign('status_id')->references('id')->on('status');
            $table->foreign('pessoas_id')->references('id')->on('pessoas');
            $table->foreign('transporte_id')->references('id')->on('transportes');
            $table->foreign('prioridade_id')->references('id')->on('prioridades');
        });



    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedidos');
    }
};
