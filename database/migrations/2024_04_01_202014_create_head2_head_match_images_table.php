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
        Schema::create('head2_head_match_images', function (Blueprint $table) {

            $table->increments('id');
            $table->string('title')->default('H2H match Pic');
            $table->string('path');
            $table->integer('Head2HeadMatch_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('head2_head_match_images');
    }
};
