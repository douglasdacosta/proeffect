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
        Schema::create('historicos_estoque', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('estoque_id')->unsigned();
            $table->text('historico')->nullable();
            $table->string('status',1);
            $table->timestamps();

            $table->foreign('estoque_id')->references('id')->on('estoque');
        });


    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historicos_estoque');
    }
};
