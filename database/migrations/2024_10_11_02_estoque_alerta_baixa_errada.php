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
            $table->float('peso_material',11, 3)->nullable()->after('peso');
            $table->integer('alerta_baixa_errada')->length(11)->default('0')->after('peso_material');

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
            $table->dropColumn('alerta_baixa_errada');
        });
    }
};
