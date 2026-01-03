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
        Schema::create('ai_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_type'); // نوع المهمة: fetch_trends, generate_article
            $table->json('payload'); // بيانات المهمة
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])->default('pending');
            $table->json('result')->nullable(); // نتيجة المهمة
            $table->timestamp('scheduled_at')->nullable(); // وقت الجدولة
            $table->timestamp('started_at')->nullable(); // وقت البدء
            $table->timestamp('completed_at')->nullable(); // وقت الانتهاء
            $table->text('error_message')->nullable(); // رسالة الخطأ
            $table->timestamps();
            
            // إضافة indexes للأداء
            $table->index('status');
            $table->index('task_type');
            $table->index('scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_tasks');
    }
};
