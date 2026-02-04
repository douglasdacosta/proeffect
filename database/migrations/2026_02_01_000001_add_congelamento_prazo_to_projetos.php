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
        Schema::table('projetos', function (Blueprint $table) {
            $table->dateTime('data_entrega_congelada')->nullable()->after('data_entrega');
            $table->integer('alerta_dias_congelado')->nullable()->after('data_entrega_congelada');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projetos', function (Blueprint $table) {
            $table->dropColumn(['data_entrega_congelada', 'alerta_dias_congelado']);
        });
    }
};