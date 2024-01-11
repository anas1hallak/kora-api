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
        Schema::create('championship_requests', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('team_id');
            $table->integer('championship_id');
            $table->string('teamImage')->nullable();
            $table->string('teamName');
            $table->string('coachName');
            $table->string('ibanNumber');
            $table->string('coachPhoneNumber');
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('championship_requests');
    }
};
