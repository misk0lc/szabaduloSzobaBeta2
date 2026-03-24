<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_options', function (Blueprint $table) {
            $table->id('OptionID');
            $table->unsignedBigInteger('QuestionID');
            $table->string('OptionText', 255);
            $table->boolean('IsCorrect')->default(false);

            $table->foreign('QuestionID')
                  ->references('QuestionID')
                  ->on('questions')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_options');
    }
};
