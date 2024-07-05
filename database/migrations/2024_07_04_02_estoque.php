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
        Schema::create('estoque', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('material_id')->unsigned()->index();
            $table->date('data');
            $table->string('nota_fiscal',100)->nullable();
            $table->bigInteger('fornecedor_id')->unsigned()->index();
            $table->integer('lote')->length(11)->nullable();
            $table->float('valor_unitario',11, 2)->nullable();
            $table->float('valor',11, 2)->nullable();
            $table->float('imposto',11, 2)->nullable();
            $table->float('total',11, 2)->nullable();
            $table->boolean('VD')->nullable();//venda direta
            $table->boolean('MO')->nullable(); //mão de obra
            $table->integer('qtde_chapa_peca')->length(11)->nullable();
            $table->integer('qtde_por_pacote')->length(11)->nullable();
            $table->string('status',1);
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
        Schema::dropIfExists('estoque');
    }
};
