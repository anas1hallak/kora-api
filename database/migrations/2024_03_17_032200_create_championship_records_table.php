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
        Schema::create('championship_records', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('team_id');
            $table->integer('championship_id');
            $table->string('championshipName');
            $table->integer('numOfParticipants')->default(16);
            $table->double('prize1');
            $table->double('prize2');
            $table->double('entryPrice');
            $table->date('startDate');
            $table->date('endDate');
            $table->string('termsAndConditions')->default('No Terms And Conditions');
            $table->string('firstWinner')->default('TBD');
            $table->string('secondWinner')->default('TBD');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('championship_records');
    }
};
