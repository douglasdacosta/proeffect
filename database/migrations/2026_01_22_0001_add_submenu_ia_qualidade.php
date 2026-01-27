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
        DB::table('submenus')->insert([
            'menu_id' => 6,
            'nome' => 'IA Qualidade',
            'rota' => 'iaqualidade',
            'icon' => 'fa fa-fw fa-arrow-right',
            'icon_color' => 'yellow',
            'status' => 'A',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('submenus')->where('nome', 'IA Qualidade')->delete();
    }
};
