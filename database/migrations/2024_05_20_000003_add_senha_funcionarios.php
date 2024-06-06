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
            $table->string('senha')->after('funcao')->nullable();;
        });

        DB::table('funcionarios')->insert(
            [
                [ 'nome' => 'Fabio', 'status' => 'A', 'senha' => 'fqwer'],
            ]
        );

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('funcionarios', function($table) {
            $table->dropColumn('senha');
        });
    }
};
