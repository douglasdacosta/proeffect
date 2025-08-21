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

        Schema::table('maquinas', function($table) {
            $table->time('segunda_inicio')->after('prazo_entrega')->nullable();
            $table->time('segunda_almoco_inicio')->after('segunda_inicio')->nullable();
            $table->time('segunda_almoco_fim')->after('segunda_almoco_inicio')->nullable();
            $table->time('segunda_fim')->after('segunda_almoco_fim')->nullable();
            $table->time('terca_inicio')->after('segunda_fim')->nullable();
            $table->time('terca_almoco_inicio')->after('terca_inicio')->nullable();
            $table->time('terca_almoco_fim')->after('terca_almoco_inicio')->nullable();
            $table->time('terca_fim')->after('terca_almoco_fim')->nullable();
            $table->time('quarta_inicio')->after('terca_fim')->nullable();
            $table->time('quarta_almoco_inicio')->after('quarta_inicio')->nullable();
            $table->time('quarta_almoco_fim')->after('quarta_almoco_inicio')->nullable();
            $table->time('quarta_fim')->after('quarta_almoco_fim')->nullable();
            $table->time('quinta_inicio')->after('quarta_fim')->nullable();
            $table->time('quinta_almoco_inicio')->after('quinta_inicio')->nullable();
            $table->time('quinta_almoco_fim')->after('quinta_almoco_inicio')->nullable();
            $table->time('quinta_fim')->after('quinta_almoco_fim')->nullable();
            $table->time('sexta_inicio')->after('quinta_fim')->nullable();
            $table->time('sexta_almoco_inicio')->after('sexta_inicio')->nullable();
            $table->time('sexta_almoco_fim')->after('sexta_almoco_inicio')->nullable();
            $table->time('sexta_fim')->after('sexta_almoco_fim')->nullable();
            $table->time('sabado_inicio')->after('sexta_fim')->nullable();
            $table->time('sabado_almoco_inicio')->after('sabado_inicio')->nullable();
            $table->time('sabado_almoco_fim')->after('sabado_almoco_inicio')->nullable();
            $table->time('sabado_fim')->after('sabado_almoco_fim')->nullable();
            $table->time('domingo_inicio')->after('sabado_fim')->nullable();
            $table->time('domingo_almoco_inicio')->after('domingo_inicio')->nullable();
            $table->time('domingo_almoco_fim')->after('domingo_almoco_inicio')->nullable();
            $table->time('domingo_fim')->after('domingo_almoco_fim')->nullable();
        });

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('maquinas', function($table) {
            $table->dropColumn('segunda_inicio');
            $table->dropColumn('segunda_almoco_inicio');
            $table->dropColumn('segunda_almoco_fim');
            $table->dropColumn('segunda_fim');
            $table->dropColumn('terca_inicio');
            $table->dropColumn('terca_almoco_inicio');
            $table->dropColumn('terca_almoco_fim');
            $table->dropColumn('terca_fim');
            $table->dropColumn('quarta_inicio');
            $table->dropColumn('quarta_almoco_inicio');
            $table->dropColumn('quarta_almoco_fim');
            $table->dropColumn('quarta_fim');
            $table->dropColumn('quinta_inicio');
            $table->dropColumn('quinta_almoco_inicio');
            $table->dropColumn('quinta_almoco_fim');
            $table->dropColumn('quinta_fim');
            $table->dropColumn('sexta_inicio');
            $table->dropColumn('sexta_almoco_inicio');
            $table->dropColumn('sexta_almoco_fim');
            $table->dropColumn('sexta_fim');
            $table->dropColumn('sabado_inicio');
            $table->dropColumn('sabado_almoco_inicio');
            $table->dropColumn('sabado_almoco_fim');
            $table->dropColumn('sabado_fim');
            $table->dropColumn('domingo_inicio');
            $table->dropColumn('domingo_almoco_inicio');
            $table->dropColumn('domingo_almoco_fim');
            $table->dropColumn('domingo_fim');
        });
    }
};

