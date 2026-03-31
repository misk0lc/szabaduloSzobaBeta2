<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leaderboard', function (Blueprint $table) {
            $table->unsignedBigInteger('UserID')->primary();
            $table->integer('Score')->default(0);
            $table->integer('LevelsCompleted')->default(0);
            $table->integer('TimeTotal')->default(0);
            $table->integer('HintsUsed')->default(0);

            $table->foreign('UserID')
                  ->references('UserID')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

 
    public function down(): void
    {
        Schema::dropIfExists('leaderboard');
    }
};
