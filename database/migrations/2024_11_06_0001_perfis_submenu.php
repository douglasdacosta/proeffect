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
        Schema::create('perfil_submenu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perfil_id')->constrained('perfis')->onDelete('cascade');
            $table->foreignId('submenu_id')->constrained('submenus')->onDelete('cascade');
            $table->timestamps();
        });

    }


/**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()   {
        Schema::dropIfExists('perfis_submenus');
    }
};
