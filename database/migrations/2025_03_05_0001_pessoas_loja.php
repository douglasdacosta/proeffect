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
            $table->integer('loja')->length(11)->default(0)->after('fornecedor');
        });

    }


/**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()   {
        Schema::table('pessoas', function($table) {
            $table->dropColumn('loja');
        });
    }
};
