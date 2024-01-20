<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::create('ficha_tecnica', function (Blueprint $table) {
            $table->id();
            $table->string('ep',40);        
            $table->time('tempo_usinagem')->nullable();
            $table->time('tempo_acabamento')->nullable();
            $table->time('tempo_montagem')->nullable();
            $table->time('tempo_montagem_torre')->nullable();
            $table->time('tempo_inspecao')->nullable(); 
            $table->boolean('status');
            $table->timestamps();
            
        });
    }

    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
