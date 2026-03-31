<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_answers', function (Blueprint $table) {
            $table->id('AnswerID');
            $table->unsignedBigInteger('UserID');
            $table->unsignedBigInteger('QuestionID');
            $table->string('GivenAnswer', 255);
            $table->boolean('IsCorrect');
            $table->dateTime('AnsweredAt')->useCurrent();

            $table->foreign('UserID')
                  ->references('UserID')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('QuestionID')
                  ->references('QuestionID')
                  ->on('questions')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_answers');
    }
};
