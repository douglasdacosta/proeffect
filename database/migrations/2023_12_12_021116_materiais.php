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
        Schema::create('materiais', function (Blueprint $table) {
            $table->id();
            $table->string('codigo',40)->unique();
            $table->string('material',100);
            $table->integer('espessura')->length(11)->nullable();
            $table->integer('unidadex')->length(11)->nullable();
            $table->integer('unidadey')->length(11)->nullable();
            $table->time('tempo_montagem_torre')->nullable();
            $table->float('valor',11, 2)->nullable();
            $table->string('peca_padrao',1);
            $table->string('status',1);
            $table->timestamps();
        });


        DB::table('materiais')->insert(
            [
                [
                    'codigo'=>'T07',
                    'material'=>'Torre 07',
                    'tempo_montagem_torre'=> '00:07',
                    'espessura' => null,
                    'unidadex' => null,
                    'peca_padrao' => 1,
                    'unidadey' => null,
                    'valor' => '1.50',
                    'status' => 'A'
                ],
                [
                    'codigo'=>'T08',
                    'material'=>'Torre 08',
                    'tempo_montagem_torre'=> '00:07',
                    'espessura' => null,
                    'unidadex' => null,
                    'peca_padrao' => 1,
                    'unidadey' => null,
                    'valor' => '1.00',
                    'status' => 'A'
                ],
                [
                    'codigo'=>'PR03',
                    'material'=>'PSAI Preto',
                    'tempo_montagem_torre'=> '',
                    'espessura' => '3',
                    'unidadex' => '1200',
                    'peca_padrao' => 2,
                    'unidadey' => '950',
                    'valor' => '95.50',
                    'status' => 'A'
                ],
                [
                    'codigo'=>'CZ03',
                    'material'=>'PSAI Cinza Claro',
                    'tempo_montagem_torre'=> '',
                    'espessura' => '3',
                    'unidadex' => '1000',
                    'unidadey' => '1000',
                    'peca_padrao' => 2,
                    'valor' => '150.50',
                    'status' => 'A'
                ]
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
        Schema::dropIfExists('materiais');
    }
};
