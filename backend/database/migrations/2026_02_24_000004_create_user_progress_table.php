<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_progress', function (Blueprint $table) {
            $table->id('ProgressID');
            $table->unsignedBigInteger('UserID');
            $table->unsignedBigInteger('LevelID');
            $table->boolean('Completed')->default(false);
            $table->integer('TimeSpent')->default(0);
            $table->dateTime('CompletedAt')->nullable();

            $table->unique(['UserID', 'LevelID']);

            $table->foreign('UserID')
                  ->references('UserID')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('LevelID')
                  ->references('LevelID')
                  ->on('levels')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_progress');
    }
};
