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
        Schema::table('renovacoes', function (Blueprint $table) {
            $table->integer('lido')->length(11)->default(0)->after('status');
            $table->dateTime('data_hora_lido')->nullable()->after('lido');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('renovacoes', function (Blueprint $table) {
            $table->dropColumn(['lido', 'data_hora_lido']);
        });
    }
};
