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
        Schema::create('status_projetos', function (Blueprint $table) {
            $table->id();
            $table->string('nome',100);
            $table->string('status',1);
            $table->timestamps();
        });

        DB::table('status_projetos')->insert(
            [
                [ 'id' => 1, 'nome' => 'Vendas', 'status' => 'A'],
                [ 'id' => 2, 'nome' => 'Reunião', 'status' => 'A'],
                [ 'id' => 3, 'nome' => 'Desenvolvimento', 'status' => 'A'],
                [ 'id' => 4, 'nome' => 'Projetos', 'status' => 'A'],
                [ 'id' => 5, 'nome' => 'Expedição', 'status' => 'A'],
                [ 'id' => 6, 'nome' => 'Cancelado', 'status' => 'A'],
                [ 'id' => 7, 'nome' => 'Em Preparação', 'status' => 'A']

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
        Schema::dropIfExists('status_projetos');
    }
};
