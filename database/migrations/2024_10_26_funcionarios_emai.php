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

        Schema::table('funcionarios', function($table) {
            $table->string('email')->unique()->nullable()->after('nome');
            $table->bigInteger('perfil')->unsigned()->index()->default(1)->after('nome');
            $table->foreign('perfil')->references('id')->on('perfis');
        });

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('funcionarios', function($table) {
            $table->dropColumn('email');
        });
    }
};
