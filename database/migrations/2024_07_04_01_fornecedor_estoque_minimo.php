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
        Schema::table('pessoas', function($table) {
            $table->boolean('fornecedor')->after('email')->nullable();
        });

        Schema::table('materiais', function($table) {
            $table->float('estoque_minimo',11, 2)->length(11)->after('valor');
        });

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pessoas', function($table) {
            $table->dropColumn('fornecedor');
        });
        Schema::table('materiais', function($table) {
            $table->dropColumn('estoque_minimo');
        });
    }
};
