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
        Schema::create('chat_histories', function (Blueprint $table) {
        $table->id();
            $table->unsignedBigInteger('chattable_id');
            $table->string('chattable_type');
            $table->string('role');
            $table->text('content');
            $table->timestamps();
            $table->index(['chattable_id', 'chattable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_histories');
    }
};
