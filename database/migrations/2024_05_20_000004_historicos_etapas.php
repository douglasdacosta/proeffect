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
        Schema::table('historicos_etapas', function($table) {
            $table->string('select_tipo_manutencao')->after('funcionarios_id')->nullable();;
            $table->string('select_motivo_pausas')->after('select_tipo_manutencao')->nullable();;
            $table->string('texto_quantidade')->after('select_motivo_pausas')->nullable();;
        });

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('historicos_etapas', function($table) {
            $table->dropColumn('select_tipo_manutencao');
            $table->dropColumn('select_motivo_pausas');
            $table->dropColumn('texto_quantidade');
        });
    }
};
