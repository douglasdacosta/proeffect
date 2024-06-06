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
        Schema::table('caixas_pedidos', function($table) {
            $table->integer('material_id')->length(11)->after('pedidos_id')->nullable();
            $table->integer('quantidade')->length(11)->after('material_id')->nullable();
        });

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('caixas_pedidos', function($table) {
            $table->dropColumn('material_id');
            $table->dropColumn('quantidade');
        });
    }
};
