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
           $table-> string('message');
           $table->integer('team_id');

            $table->timestamps();




        });
    }

   
    public function down(): void
    {
        Schema::dropIfExists('championship_requests');
        
    }
};

