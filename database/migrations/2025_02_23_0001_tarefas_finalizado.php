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
        Schema::table('tarefas', function($table) {
            $table->dateTime('finalizado')->nullable()->after('mensagem');
            $table->dateTime('data_atividade')->nullable()->after('finalizado');
        });

    }


/**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()   {
        Schema::table('tarefas', function($table) {
            $table->dropColumn('finalizado');
            $table->dropColumn('data_atividade');
        });
    }
};
