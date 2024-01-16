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
        Schema::create('championships', function (Blueprint $table) {


            $table->increments('id');
            $table->string('championshipName');
            $table->integer('numOfParticipants')->default(16);
            $table->double('prize1');
            $table->double('prize2');
            $table->double('entryPrice');
            $table->date('startDate');
            $table->date('endDate');
            $table->string('status')->default('open');
            $table->timestamps();



        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('championships');
    }
};
