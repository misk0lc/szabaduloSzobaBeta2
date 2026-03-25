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
        Schema::table('multiplayer_sessions', function (Blueprint $table) {
            $table->enum('Status', ['waiting', 'playing', 'finished', 'abandoned'])
                  ->default('waiting')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('multiplayer_sessions', function (Blueprint $table) {
            $table->enum('Status', ['waiting', 'playing', 'finished'])
                  ->default('waiting')
                  ->change();
        });
    }
};
