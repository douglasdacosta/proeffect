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
        Schema::create('tarefas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funcionario_id')->constrained('funcionarios')->onDelete('cascade');
            $table->foreignId('funcionario_criador_id')->constrained('funcionarios')->onDelete('cascade');
            $table->dateTime('data_hora')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('lido')->length(11)->default(0);
            $table->dateTime('data_hora_lido')->nullable();
            $table->text('mensagem');
            $table->string('status',1)->default('A');
            $table->timestamps();
        });

    }


/**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()   {
        Schema::dropIfExists('tarefas');
    }
};
