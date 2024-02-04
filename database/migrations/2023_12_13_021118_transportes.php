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
        Schema::create('transportes', function (Blueprint $table) {
            $table->id();
            $table->string('nome',100);
            $table->string('status',1);
            $table->timestamps();
        });


        DB::table('transportes')->insert(
            [
                [ 'nome' =>'Transportadora Cliente', 'status' => 'A'],
                [ 'nome' =>'Cliente Retira', 'status' => 'A'],
                [ 'nome' =>'Cliente Correio', 'status' => 'A'],
                [ 'nome' =>'PROG', 'status' => 'A'],
                [ 'nome' =>'QUA', 'status' => 'A'],
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
        Schema::dropIfExists('transportes');
    }
};
