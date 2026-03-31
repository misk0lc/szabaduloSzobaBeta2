<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hints', function (Blueprint $table) {
            $table->id('HintID');
            $table->unsignedBigInteger('QuestionID');
            $table->text('HintText');
            $table->integer('Cost');
            $table->integer('HintOrder');

            $table->foreign('QuestionID')
                  ->references('QuestionID')
                  ->on('questions')
                  ->onDelete('cascade');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('hints');
    }
};
