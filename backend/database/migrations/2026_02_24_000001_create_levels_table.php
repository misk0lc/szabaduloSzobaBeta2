<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->id('LevelID');
            $table->string('Name', 100);
            $table->text('Description')->nullable();
            $table->string('Category', 50)->default('Könnyed');
            $table->integer('OrderNumber')->unique();
            $table->boolean('IsActive')->default(true);
            $table->string('BackgroundUrl', 500)->nullable();
            $table->dateTime('CreatedAt')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('levels');
    }
};
