<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('dashboards', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('status',1)->default('A');
            $table->timestamps();
        });

        DB::table('dashboards')->insert(
            [
                [ 'id' => '1', 'nome' => 'Vendas'],
                [ 'id' => '2', 'nome' => 'Atrasos'],
                [ 'id' => '3', 'nome' => 'Comparativo'],
                [ 'id' => '4', 'nome' => 'Previsão'],
                [ 'id' => '5', 'nome' => 'Tarefas'],
            ]
        );
    }


/**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()   {
        Schema::dropIfExists('dashboard');
    }
};
