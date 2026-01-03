<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Gemini AI, OpenAI, OpenRouter
            $table->string('type'); // gemini, openai, openrouter, claude
            $table->text('api_key')->nullable(); // سيتم تشفيره
            $table->string('model')->nullable(); // gemini-pro, gpt-4, etc.
            $table->string('base_url')->nullable(); // للـ custom endpoints
            $table->json('settings')->nullable(); // temperature, max_tokens, etc.
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->integer('priority')->default(0); // للترتيب
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('type');
            $table->index('is_active');
            $table->index('is_default');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_providers');
    }
};
