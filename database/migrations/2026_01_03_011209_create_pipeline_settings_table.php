<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pipeline_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // trends_count, min_words, etc.
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, boolean, json, array
            $table->string('group')->default('general'); // general, content, search, etc.
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('key');
            $table->index('group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pipeline_settings');
    }
};
