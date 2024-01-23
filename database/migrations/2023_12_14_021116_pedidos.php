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
            $table->integer('qtde')->length(11);
            $table->dateTime('data_gerado');
            $table->dateTime('data_entrega');
            $table->bigInteger('status_id')->unsigned()->index();
            $table->text('observacao')->length(11)->nullable();
            $table->float('valor',11, 2)->nullable();
            $table->boolean('status')->nullable();
            $table->timestamps();

            $table->foreign('fichatecnica_id')->references('id')->on('ficha_tecnica');
            $table->foreign('status_id')->references('id')->on('status');
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
