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
        Schema::create('categoria_tela', function (Blueprint $table) {
            $table->id();
            $table->string('nome',100);
        });

        DB::table('categoria_tela')->insert(
            [
                ['id'=> '1', 'nome'=>'Cadastros'],
                ['id'=> '2', 'nome'=>'Produção'],
                ['id'=> '3', 'nome'=>'Configurações'],
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
        Schema::dropIfExists('categoria_tela');
    }
};
