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
        Schema::create('head2_head_requests', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('Head2HeadMatch_id');

            $table->integer('team1_id')->nullable();
            $table->integer('team2_id')->nullable();
         
            $table->string('ibanNumber1');
            $table->string('ibanNumber2');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('head2_head_requests');
    }
};
