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
        Schema::create('etapas_projetos', function (Blueprint $table) {
            $table->id();
            $table->string('nome',100);
            $table->string('status',1);
            $table->timestamps();
        });

        DB::table('etapas_projetos')->insert(
            [
                [ 'id' => 1, 'nome' => 'Estudo', 'status' => 'A'],
                [ 'id' => 2, 'nome' => 'Projeto', 'status' => 'A'],
                [ 'id' => 3, 'nome' => 'Programação', 'status' => 'A'],
                [ 'id' => 4, 'nome' => 'Fabrica', 'status' => 'A'],
                [ 'id' => 5, 'nome' => 'Finalizado', 'status' => 'A']

            ]
        );
    }


   /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('etapas_projetos');
    }
};
