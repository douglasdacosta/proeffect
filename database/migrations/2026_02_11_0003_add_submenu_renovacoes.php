<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

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
            'menu_id' => 1,
            'nome' => 'Renovações',
            'rota' => 'renovacoes',
            'icon' => 'fa fa-fw fa-arrow-right',
            'icon_color' => 'yellow',
            'status' => 'A',
            'ordem' => 0,
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
        DB::table('submenus')->where('rota', 'renovacoes')->delete();
    }
};
