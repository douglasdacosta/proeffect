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
        // Tabela de submenus, relacionada com a tabela menus
        Schema::create('submenus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->string('nome');
            $table->string('rota')->nullable();
            $table->string('icon',100)->nullable();
            $table->string('label',100)->nullable();
            $table->string('label_color',100)->nullable();
            $table->string('icon_color',100)->nullable();
            $table->integer('ordem')->default(0);
            $table->string('status',1)->default('A');
            $table->timestamps();
        });
        // Inserção de dados de exemplo na tabela submenus
        DB::table('submenus')->insert(
            [
                //Lançamentos
                ['id'=> '1', 'menu_id'=>'1', 'label'=>'', 'nome'=> 'Clientes', 'rota'=> 'clientes', 'icon' => 'fa fa-fw fa-arrow-right', 'icon_color' => 'yellow'],
                ['id'=> '2', 'menu_id'=>'1', 'label'=>'', 'nome'=> 'Colaboradores', 'rota'=> 'funcionarios', 'icon' => 'fa fa-fw fa-arrow-right', 'icon_color' => 'yellow'],
                ['id'=> '3', 'menu_id'=>'1', 'label'=>'', 'nome'=> 'Materiais', 'rota'=> 'materiais', 'icon' => 'fa fa-fw fa-arrow-right', 'icon_color' => 'yellow'],
                ['id'=> '4', 'menu_id'=>'1', 'label'=>'', 'nome'=> 'Status', 'rota'=> 'status', 'icon' => 'fa fa-fw fa-arrow-right', 'icon_color' => 'yellow'],
                ['id'=> '16', 'menu_id'=>'1', 'label'=>'', 'nome'=> 'Perfis', 'rota'=> 'perfis', 'icon' => 'fa fa-fw fa-arrow-right', 'icon_color' => 'yellow'],
                ['id'=> '5', 'menu_id'=>'2', 'label'=>'', 'nome'=> 'Ficha técnica', 'rota'=> 'fichatecnica', 'icon' => 'fa fa-fw fa-arrow-right', 'icon_color' => 'yellow'],
                ['id'=> '6', 'menu_id'=>'3', 'label'=>'', 'nome'=> 'Maquinas', 'rota'=> 'maquinas', 'icon' => 'fa fa-fw fa-arrow-right', 'icon_color' => 'yellow'],
                //relatórios
                ['id'=> '7', 'menu_id'=>'4', 'label'=>'', 'nome'=> 'Followup', 'rota'=> 'followup', 'icon' => 'fa fa-fw fa-arrow-right', 'icon_color' => 'yellow'],
                ['id'=> '8', 'menu_id'=>'5', 'label'=>'', 'nome'=> 'Produção de Maquinas', 'rota'=> 'producao-maquinas', 'icon' => 'fa fa-fw fa-arrow-right', 'icon_color' => 'yellow'],
                //Analise
                ['id'=> '9', 'menu_id'=>'6', 'label'=>'', 'nome'=> 'Pedidos', 'rota'=> 'pedidos', 'icon' => 'fa fa-fw fa-arrow-right', 'icon_color' => 'yellow'],
                ['id'=> '10', 'menu_id'=>'6', 'label'=>'', 'nome'=> 'Orçamentos', 'rota'=> 'orcamentos', 'icon' => 'fa fa-fw fa-arrow-right', 'icon_color' => 'yellow'],
                ['id'=> '11', 'menu_id'=>'6', 'label'=>'', 'nome'=> 'Estoque', 'rota'=> 'estoque', 'icon' => 'fa fa-fw fa-arrow-right', 'icon_color' => 'yellow'],
                ['id'=> '12', 'menu_id'=>'6', 'label'=>'', 'nome'=> 'Consumo Materiais', 'rota'=> 'consumo-materiais', 'icon' => 'fa fa-fw fa-arrow-right', 'icon_color' => 'yellow'],
                ['id'=> '13', 'menu_id'=>'6', 'label'=>'', 'nome'=> 'Painéis', 'rota'=> 'paineis', 'icon' => 'fas fa-solar-panel', 'icon_color' => 'yellow'],
                ['id'=> '14', 'menu_id'=>'6', 'label'=>'', 'nome'=> 'Previsão material', 'rota'=> 'relatorio-previsao-material', 'icon' => 'fa fa-fw fa-arrow-right', 'icon_color' => 'yellow'],
                //Configurações
                ['id'=> '15', 'menu_id'=>'7', 'label'=>'', 'nome'=> 'Conta', 'rota'=> 'admin/settings', 'icon' => 'fa fa-fw fa-arrow-right', 'icon_color' => 'yellow'],
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
        Schema::dropIfExists('submenus');
    }
};
