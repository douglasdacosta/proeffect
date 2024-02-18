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
        Schema::create('status', function (Blueprint $table) {
            $table->id();
            $table->string('nome',100);
            $table->boolean('alertacliente');
            $table->string('status',1);
            $table->timestamps();
        });

        DB::table('status')->insert(
            [
                [ 'nome' => 'Imprimir', 'alertacliente' => 1, 'status' => 'A'],
                [ 'nome' => 'Em Preparação', 'alertacliente' => 1, 'status' => 'A'],
                [ 'nome' => 'Aguardando Material', 'alertacliente' => 1, 'status' => 'A'],
                [ 'nome' => 'Usinagem', 'alertacliente' => 1, 'status' => 'A'],
                [ 'nome' => 'Acabamento', 'alertacliente' => 1, 'status' => 'A'],
                [ 'nome' => 'Montagem', 'alertacliente' => 1, 'status' => 'A'],
                [ 'nome' => 'Inspeção', 'alertacliente' => 1, 'status' => 'A'],
                [ 'nome' => 'Embalar', 'alertacliente' => 1, 'status' => 'A'],
                [ 'nome' => 'Expedição', 'alertacliente' => 1, 'status' => 'A'],
                [ 'nome' => 'Estoque   ', 'alertacliente' => 1, 'status' => 'A'],
                [ 'nome' => 'Entregue', 'alertacliente' => 1, 'status' => 'A'],

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
        Schema::dropIfExists('status');
    }
};
