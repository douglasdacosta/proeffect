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

        Schema::table('pedidos', function($table) {
            $table->date('data_antecipacao')->nullable()->after('observacao');
            $table->time('hora_antecipacao')->nullable()->after('data_antecipacao');
        });

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pedidos', function($table) {
            $table->dropColumn('data_antecipacao');
            $table->dropColumn('hora_antecipacao');
        });
    }
};
