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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        DB::table('users')->insert(
                array(
                        'id' => 1, 
                        'name'=>'Douglas da Costa', 
                        'email'=> 'doug.d.costa@gmail.com', 
                        'password' => '$2y$10$JuNPSfO.DYgE4OZ8aPq/6.ucGNXXVYUNsRt3PPus5YJvHf1lQDeDm'
                    )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};