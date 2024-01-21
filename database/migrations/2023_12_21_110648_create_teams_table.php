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
        Schema::create('teams', function (Blueprint $table) {
            $table->increments('id');
            $table->string('teamName')->unique();
            $table->integer('points');
            $table->integer('wins');
            $table->double('rate')->default(0.5);
            $table->string('termsAndConditions')->default('No Terms And Conditions');
            $table->string('coachName');
            $table->string('coachPhoneNumber');
            $table->string('coachEmail');
            $table->integer('user_id');
            $table->timestamps();

        });
    }

   
    public function down(): void
    {
        Schema::dropIfExists('teams');
        
    }
};
