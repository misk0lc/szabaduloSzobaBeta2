<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id('ReportID');
            $table->unsignedBigInteger('UserID')->nullable();
            $table->foreign('UserID')->references('UserID')->on('users')->onDelete('set null');
            $table->string('Title', 100);
            $table->string('Category', 50)->default('bug');
            $table->string('ContactEmail', 100)->nullable();
            $table->text('Message');
            $table->string('Page', 100)->nullable();
            $table->enum('Status', ['new', 'seen', 'resolved'])->default('new');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
