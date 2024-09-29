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

        DB::statement('ALTER TABLE estoque MODIFY COLUMN lote VARCHAR(100)');

        Schema::table('estoque', function($table) {
            $table->float('valor_mo',11, 2)->nullable()->after('MO');
        });

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE estoque MODIFY COLUMN lote INTEGER(11)');

        Schema::table('estoque', function($table) {
            $table->dropColumn('valor_mo');
        });
    }
};
