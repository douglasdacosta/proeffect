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
        Schema::create('categoria_menus', function (Blueprint $table) {
            $table->id();
            $table->string('nome',100);
        });

        DB::table('categoria_menus')->insert(
            [
                ['id'=> '1', 'nome'=>'Lançamentos'],
                ['id'=> '2', 'nome'=>'Relatórios'],
                ['id'=> '3', 'nome'=>'Análise'],
                ['id'=> '4', 'nome'=>'Configuração'],
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
        Schema::dropIfExists('categoria_menus');
    }
};
