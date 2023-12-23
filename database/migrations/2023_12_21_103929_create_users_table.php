<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id'); 
            $table->string('fullName');
            $table->string('phoneNumber')->unique();
            $table->string('password');
            $table->string('email')->unique();
            $table->string('playerNumber')->nullable();
            $table->string('placeOfPlayer')->nullable();
            $table->string('selected')->nullable();
            $table->integer('team_id')->nullable();
            $table->integer('role_id')->default(0);
            $table->integer('elo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

