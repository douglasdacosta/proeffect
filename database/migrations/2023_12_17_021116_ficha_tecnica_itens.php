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
        Schema::create('ficha_tecnica_itens', function (Blueprint $table) {
            $table->id();            
            $table->bigInteger('fichatecnica_id')->unsigned()->index();            
            $table->bigInteger('materiais_id')->unsigned()->index();            
            $table->string('blank',40);
            $table->integer('qtde_blank')->length(11);            
            $table->integer('medidax')->length(11)->nullable();
            $table->integer('mediday')->length(11)->nullable();
            $table->float('tempo_usinagem')->nullable();
            $table->float('tempo_acabamento')->nullable();
            $table->float('tempo_montagem')->nullable();
            $table->float('tempo_montagem_torre')->nullable();
            $table->float('tempo_inspecao')->nullable();
            $table->boolean('status');
            $table->timestamps();

            $table->foreign('fichatecnica_id')->references('id')->on('ficha_tecnica');
            $table->foreign('materiais_id')->references('id')->on('materiais');


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
