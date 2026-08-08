<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ia_qualidade_leads', function (Blueprint $table) {
            $table->string('descricao')->nullable()->after('ep');
        });
    }

    public function down()
    {
        Schema::table('ia_qualidade_leads', function (Blueprint $table) {
            $table->dropColumn('descricao');
        });
    }
};
