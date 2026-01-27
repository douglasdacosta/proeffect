<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Add submenu item under menu_id=7
        DB::table('submenus')->insert([
            'menu_id' => 7,
            'nome' => 'Configuração IA',
            'rota' => 'configuracao-ia',
            'icon' => 'fa fa-fw fa-cog',
            'icon_color' => 'yellow',
            'status' => 'A',
            'ordem' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        DB::table('submenus')->where('rota', 'configuracao-ia')->delete();
    }
};
