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
        Schema::create('ficha_tecnica', function (Blueprint $table) {
            $table->id();
            $table->string('ep',40);
            $table->time('tempo_usinagem')->nullable();
            $table->time('tempo_acabamento')->nullable();
            $table->time('tempo_montagem')->nullable();
            $table->time('tempo_montagem_torre')->nullable();
            $table->time('tempo_inspecao')->nullable();

            $table->string('alerta_usinagem1',200)->nullable();
            $table->string('alerta_usinagem2',200)->nullable();
            $table->string('alerta_usinagem3',200)->nullable();
            $table->string('alerta_usinagem4',200)->nullable();
            $table->string('alerta_usinagem5',200)->nullable();
            $table->string('alerta_acabamento1',200)->nullable();
            $table->string('alerta_acabamento2',200)->nullable();
            $table->string('alerta_acabamento3',200)->nullable();
            $table->string('alerta_acabamento4',200)->nullable();
            $table->string('alerta_acabamento5',200)->nullable();
            $table->string('alerta_montagem1',200)->nullable();
            $table->string('alerta_montagem2',200)->nullable();
            $table->string('alerta_montagem3',200)->nullable();
            $table->string('alerta_montagem4',200)->nullable();
            $table->string('alerta_montagem5',200)->nullable();
            $table->string('alerta_inspecao1',200)->nullable();
            $table->string('alerta_inspecao2',200)->nullable();
            $table->string('alerta_inspecao3',200)->nullable();
            $table->string('alerta_inspecao4',200)->nullable();
            $table->string('alerta_inspecao5',200)->nullable();
            $table->string('alerta_expedicao1',200)->nullable();
            $table->string('alerta_expedicao2',200)->nullable();
            $table->string('alerta_expedicao3',200)->nullable();
            $table->string('alerta_expedicao4',200)->nullable();
            $table->string('alerta_expedicao5',200)->nullable();
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
        Schema::dropIfExists('ficha_tecnica');
    }
};
