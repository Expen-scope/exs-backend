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
  Schema::create('chat_sessions', function (Blueprint $table) {
        $table->id();
        $table->morphs('sessionable'); // This will create sessionable_id and sessionable_type
        $table->string('token')->unique();
        $table->timestamp('expires_at');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_sessions');
    }
};
