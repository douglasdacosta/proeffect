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
        Schema::create('permissoes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('users_id')->unsigned()->index();
            $table->bigInteger('telas_id')->unsigned()->index();
            $table->bigInteger('permissoes_id')->unsigned()->index();
            $table->timestamps();

            $table->foreign('users_id')->references('id')->on('users');
            $table->foreign('permissoes_id')->references('id')->on('permissoes');
            $table->foreign('telas_id')->references('id')->on('telas');

        });

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissoes');
    }
};
