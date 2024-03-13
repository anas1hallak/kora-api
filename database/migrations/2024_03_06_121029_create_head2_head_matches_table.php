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
        Schema::create('head2_head_matches', function (Blueprint $table) {
            
            $table->increments('id');
            
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->string('location')->nullable();
            $table->string('stad')->nullable();

            $table->integer('team1_id')->nullable();
            $table->integer('team2_id')->nullable();
            $table->integer('winner')->nullable();

            $table->string('status')->nullable();

            $table->string('ibanNumber1')->nullable();
            $table->string('ibanNumber2')->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('head2_head_matches');
    }
};
