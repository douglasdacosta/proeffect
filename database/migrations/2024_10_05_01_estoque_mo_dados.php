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

        Schema::table('estoque', function($table) {
            $table->float('valor_kg_mo',11, 2)->default('0')->after('valor_mo');
            $table->float('imposto_mo',11, 2)->default('0')->after('valor_kg_mo');
            $table->float('total_mo',11, 2)->default('0')->after('imposto_mo');
            $table->integer('qtde_chapa_peca_mo')->length(11)->default('0')->after('total_mo');
            $table->integer('qtde_por_pacote_mo')->length(11)->default('0')->after('qtde_chapa_peca_mo');
            $table->text('observacaoes')->nullable()->after('qtde_por_pacote_mo');

        });

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('estoque', function($table) {
            $table->dropColumn('valor_kg_mo');
            $table->dropColumn('imposto_mo');
            $table->dropColumn('total_mo');
            $table->dropColumn('qtde_chapa_peca_mo');
            $table->dropColumn('qtde_por_pacote_mo');
            $table->dropColumn('observacaoes');
        });
    }
};
