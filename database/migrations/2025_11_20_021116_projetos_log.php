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
        Schema::create('projetos_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('projetos_id')->unsigned();
            $table->text('historico')->nullable();
            $table->timestamps();

            $table->foreign('projetos_id')->references('id')->on('projetos');
        });


    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projetos_logs');
    }
};
