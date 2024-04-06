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
        Schema::create('team_requests', function (Blueprint $table) {
            
            $table->increments('id');
            $table->integer('team_id'); 
            $table->integer('user_id');

            $table->string('fullName');
            $table->string('nationality');
            $table->string('placeOfPlayer')->nullable();
            $table->boolean('isSeen')->default(false);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_requests');
    }
};
