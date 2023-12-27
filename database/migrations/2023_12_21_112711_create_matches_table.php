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
        Schema::create('matches', function (Blueprint $table) { 
            $table->increments('id');
            $table->date('date');
            $table->time('time');
            $table->string('location');
            $table->string('stad');

            $table->integer('round_id');
            $table->integer('team1_id');
            $table->integer('team2_id');
            $table->integer('winner')->nullable();


            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
