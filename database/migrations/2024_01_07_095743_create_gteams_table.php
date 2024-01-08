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
        Schema::create('gteams', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id');
            $table->string('teamName')->nullable();
            $table->integer('points')->default(0);
            $table->integer('goals')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gteams');
    }
};
