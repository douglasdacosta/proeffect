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
        Schema::create('funcionarios', function (Blueprint $table) {
            $table->id();            
            $table->string('nome');
            $table->string('funcao')->nullable();  
            $table->string('status',1);         
            $table->timestamps();            
        });
        DB::table('funcionarios')->insert(
            [
                [ 'nome' => 'Fabio', 'status' => 'A'],
                [ 'nome'=> 'João', 'status'=> 'A'],
                [ 'nome'=> 'Maria', 'status'=> 'A'],
                [ 'nome'=> 'André', 'status'=> 'A'],
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
        Schema::dropIfExists('funcionarios');
    }
};
