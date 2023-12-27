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
            $table->string('teamName');
            $table-> integer('points');
            $table-> integer('wins');
            $table->string('termsAndConditions');
            $table->string('coachName');
            $table-> integer('user_id');




        });
    }

   
    public function down(): void
    {
        Schema::dropIfExists('teams');
        
    }
};
