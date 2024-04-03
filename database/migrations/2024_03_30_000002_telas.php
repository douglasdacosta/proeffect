<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::create('telas', function (Blueprint $table) {
            $table->id();
            $table->string('nome',100);
            $table->string('rota',100);
            $table->string('icon',100)->nullable();
            $table->string('label',100)->nullable();
            $table->string('label_color',100)->nullable();
            $table->bigInteger('categoria_tela_id')->unsigned()->index();
            $table->timestamps();

            $table->foreign('categoria_tela_id')->references('id')->on('categoria_tela');
        });

        DB::table('telas')->insert(
            [
                [
                    'id'=> '1',
                    'nome'=>'Pedidos',
                    'rota'=> 'pedidos',
                    'icon'=>'fa fa-fw fa-arrow-right',
                    'label'=>'',
                    'label_color'=>'',
                    'categoria_tela_id'=>'1'
                ],
                [
                    'id'=> '2',
                    'nome'=>'Followup',
                    'rota'=> 'followup',
                    'icon'=>'far fa-fw fa-calendar',
                    'label'=>'',
                    'label_color'=>'',
                    'categoria_tela_id'=>'1'
                ],
                [
                    'id'=> '3',
                    'nome'=>'Materiais',
                    'rota'=> 'materiais',
                    'icon'=>'far fa-fw fa-file',
                    'label'=>'',
                    'label_color'=>'',
                    'categoria_tela_id'=>'1'
                ],
                [
                    'id'=> '4',
                    'nome'=>'Consumo Materiais',
                    'rota'=> 'consumo-materiais',
                    'icon'=>'far fa-fw fa-file',
                    'label'=>'',
                    'label_color'=>'',
                    'categoria_tela_id'=>'1'
                ],
                [
                    'id'=> '5',
                    'nome'=>'Ficha técnica',
                    'rota'=> 'fichatecnica',
                    'icon'=>'far fa-fw fa-file',
                    'label'=>'',
                    'label_color'=>'',
                    'categoria_tela_id'=>'1'
                ],
                [
                    'id'=> '6',
                    'nome'=>'Status',
                    'rota'=> 'status',
                    'icon'=>'far fa-fw fa-file',
                    'label'=>'',
                    'label_color'=>'',
                    'categoria_tela_id'=>'1'
                ],
                [
                    'id'=> '7',
                    'nome'=>'Clientes',
                    'rota'=> 'clientes',
                    'icon'=>'far fa-fw fa-file',
                    'label'=>'',
                    'label_color'=>'',
                    'categoria_tela_id'=>'1'
                ],
                [
                    'id'=> '8',
                    'nome'=>'Máquinas',
                    'rota'=> 'maquinas',
                    'icon'=>'far fa-fw fa-file',
                    'label'=>'',
                    'label_color'=>'',
                    'categoria_tela_id'=>'1'
                ],
                [
                    'id'=> '9',
                    'nome'=>'Orçamentos',
                    'rota'=> 'orcamentos',
                    'icon'=>'far fa-fw fa-file',
                    'label'=>'',
                    'label_color'=>'',
                    'categoria_tela_id'=>'1'
                ],
                [
                    'id'=> '10',
                    'nome'=>'Produção de Maquinas',
                    'rota'=> 'producao-maquinas',
                    'icon'=>'far fa-fw fa-file',
                    'label'=>'',
                    'label_color'=>'',
                    'categoria_tela_id'=>'2'
                ],
                [
                    'id'=> '11',
                    'nome'=>'Conta',
                    'rota'=> 'admin/settings',
                    'icon'=>'fas fa-fw fa-user',
                    'label'=>'',
                    'label_color'=>'',
                    'categoria_tela_id'=>'3'
                ],
                [
                    'id'=> '12',
                    'nome'=>'Alterar senha',
                    'rota'=> 'admin/settings',
                    'icon'=>'fas fa-fw fa-lock',
                    'label'=>'',
                    'label_color'=>'',
                    'categoria_tela_id'=>'3'
                ],

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
        Schema::dropIfExists('telas');
    }
};
