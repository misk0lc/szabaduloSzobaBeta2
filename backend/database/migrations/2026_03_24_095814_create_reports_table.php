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
        Schema::create('reports', function (Blueprint $table) {
            $table->id('ReportID');
            $table->foreignId('UserID')->constrained('users', 'UserID')->onDelete('cascade');
            $table->string('Title', 100);
            $table->text('Message');
            $table->string('Page', 100)->nullable();  // pl. "/game" vagy "/room/3"
            $table->enum('Status', ['new', 'seen', 'resolved'])->default('new');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
