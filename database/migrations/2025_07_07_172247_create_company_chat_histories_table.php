<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('company_chat_histories', function (Blueprint $table) {
        $table->id();
        $table->foreignId('company_id')->constrained()->onDelete('cascade');
        $table->enum('role', ['user', 'assistant']);
        $table->text('content');
        $table->timestamps();
    });
    }


    public function down(): void
    {
        Schema::dropIfExists('company_chat_histories');
    }
};
