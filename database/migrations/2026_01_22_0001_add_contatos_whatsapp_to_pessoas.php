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
        Schema::table('pessoas', function (Blueprint $table) {
            $table->string('contato_venda', 100)->nullable()->after('email');
            $table->string('numero_whatsapp_venda', 20)->nullable()->after('contato_venda');
            $table->string('contato_pos_venda', 100)->nullable()->after('numero_whatsapp_venda');
            $table->string('numero_whatsapp_pos_venda', 20)->nullable()->after('contato_pos_venda');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pessoas', function (Blueprint $table) {
            $table->dropColumn('contato_venda');
            $table->dropColumn('numero_whatsapp_venda');
            $table->dropColumn('contato_pos_venda');
            $table->dropColumn('numero_whatsapp_pos_venda');
        });
    }
};
