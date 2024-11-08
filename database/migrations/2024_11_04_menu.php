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
        // Tabela de menus principais
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->foreignId('categoria_menu_id')->constrained('categoria_menus')->onDelete('cascade');
            $table->string('icon')->nullable();
            $table->integer('ordem')->default(0);
            $table->string('ativo', 1)->default('A');
            $table->timestamps();
        });

        // Inserção de dados de exemplo na tabela menus
        DB::table('menus')->insert([
            //Lançamentos
            ['id' => 1, 'nome' => 'Cadastros', 'categoria_menu_id'=>'1' , 'icon' => 'settings', 'ordem' => 1, 'ativo' => 'A'],
            ['id' => 2, 'nome' => 'Projetos', 'categoria_menu_id'=>'1' , 'icon' => 'settings', 'ordem' => 2, 'ativo' => 'A'],
            ['id' => 3, 'nome' => 'Produção', 'categoria_menu_id'=>'1' , 'icon' => 'settings', 'ordem' => 3, 'ativo' => 'A'],

            //Relatórios
            ['id' => 4, 'nome' => 'Geral', 'categoria_menu_id'=>'2' , 'icon' => 'finance', 'ordem' => 4, 'ativo' => 'A'],
            ['id' => 5, 'nome' => 'Produção', 'categoria_menu_id'=>'2' , 'icon' => 'settings', 'ordem' => 5, 'ativo' => 'A'],

            //Análise
            ['id' => 6, 'nome' => 'Análise', 'categoria_menu_id'=>'3' , 'icon' => 'production', 'ordem' => 6, 'ativo' => 'A'],
            //Configuração
            ['id' => 7, 'nome' => 'Configurações', 'categoria_menu_id'=>'4' , 'icon' => 'production', 'ordem' => 7, 'ativo' => 'A'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus');
    }
};
