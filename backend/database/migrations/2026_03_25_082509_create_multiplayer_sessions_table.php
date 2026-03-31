<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('multiplayer_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('LevelID');
            $table->foreign('LevelID')->references('LevelID')->on('levels')->onDelete('cascade');
            $table->enum('Status', ['waiting', 'playing', 'finished', 'abandoned'])->default('waiting');
            $table->json('SolvedQuestions')->default('[]');
            $table->timestamps();
        });

        Schema::create('multiplayer_session_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('SessionID');
            $table->foreign('SessionID')->references('id')->on('multiplayer_sessions')->onDelete('cascade');
            $table->unsignedBigInteger('UserID');
            $table->foreign('UserID')->references('UserID')->on('users')->onDelete('cascade');
            $table->boolean('IsReady')->default(false);
            $table->timestamps();
            $table->unique(['SessionID', 'UserID']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('multiplayer_session_users');
        Schema::dropIfExists('multiplayer_sessions');
    }
};
