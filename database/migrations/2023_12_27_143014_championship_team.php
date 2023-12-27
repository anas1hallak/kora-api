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
        Schema::create('championship_team', function (Blueprint $table) {
            $table->increments('id'); 
            $table->integer('championship_id'); 
            $table->integer('team_id'); 

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('championship_team');

    }
};
