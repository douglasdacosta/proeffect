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
            $table->integer('prazo_usinagem')->length(11)->nullable()->after('prazo_entrega');
            $table->integer('prazo_acabamento')->length(11)->nullable()->after('prazo_usinagem');
            $table->integer('prazo_montagem')->length(11)->nullable()->after('prazo_acabamento');
            $table->integer('prazo_inspecao')->length(11)->nullable()->after('prazo_montagem');
            $table->integer('prazo_embalar')->length(11)->nullable()->after('prazo_inspecao');
            $table->integer('prazo_expedicao')->length(11)->nullable()->after('prazo_embalar');
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
            $table->dropColumn('prazo_usinagem');
            $table->dropColumn('prazo_acabamento');
            $table->dropColumn('prazo_montagem');
            $table->dropColumn('prazo_inspecao');
            $table->dropColumn('prazo_embalar');
            $table->dropColumn('prazo_expedicao');
        });
    }
};
