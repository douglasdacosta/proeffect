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
        Schema::create('materiais_historicos_valores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materiais_id')->constrained('materiais')->onDelete('cascade');
            $table->float('valor',11, 2)->nullable();
            $table->timestamps();
        });

    }


/**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()   {
        Schema::dropIfExists('materiais_historicos_valores');
    }
};
