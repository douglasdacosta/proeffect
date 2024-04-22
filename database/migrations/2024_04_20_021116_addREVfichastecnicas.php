<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::table('ficha_tecnica', function($table) {
            $table->integer('rev')->after('alerta_expedicao5')->nullable();;
            $table->date('data_rev')->after('rev')->nullable();
        });
    }


 /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ficha_tecnica', function($table) {
            $table->dropColumn('rev');
            $table->dropColumn('data_rev');
        });
    }
};
