<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Providers\DateHelpers;

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
            $table->boolean('status');
            $table->timestamps();
        });

        DB::table('status')->insert(
            [
                [ 'nome' => 'Imprimir', 'alertacliente' => 1, 'status' => 1],
                [ 'nome' => 'Em Preparação', 'alertacliente' => 1, 'status' => 1], 
                [ 'nome' => 'Usinagem', 'alertacliente' => 1, 'status' => 1], 
                [ 'nome' => 'Acabamento', 'alertacliente' => 1, 'status' => 1], 
                [ 'nome' => 'Montagem', 'alertacliente' => 1, 'status' => 1], 
                [ 'nome' => 'Inspeção', 'alertacliente' => 1, 'status' => 1], 
                [ 'nome' => 'Embalar', 'alertacliente' => 1, 'status' => 1], 
                [ 'nome' => 'Expedição', 'alertacliente' => 1, 'status' => 1], 
                [ 'nome' => 'Entregue', 'alertacliente' => 1, 'status' => 1], 
                [ 'nome' => 'Estoque   ', 'alertacliente' => 1, 'status' => 1], 
                
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
        //
    }
};
