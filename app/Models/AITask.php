<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AITask extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'ai_tasks';
    
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'task_type',
        'payload',
        'status',
        'result',
        'scheduled_at',
        'started_at',
        'completed_at',
        'error_message',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'payload' => 'array',
        'result' => 'array',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Scope للمهام المعلقة
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope للمهام قيد التنفيذ
     */
    public function scopeRunning($query)
    {
        return $query->where('status', 'running');
    }

    /**
     * Scope للمهام المكتملة
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope للمهام الفاشلة
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * تحديث حالة المهمة إلى running
     */
    public function markAsRunning(): void
    {
        $this->update([
            'status' => 'running',
            'started_at' => now(),
        ]);
    }

    /**
     * تحديث حالة المهمة إلى completed
     */
    public function markAsCompleted(array $result = []): void
    {
        $this->update([
            'status' => 'completed',
            'result' => $result,
            'completed_at' => now(),
        ]);
    }

    /**
     * تحديث حالة المهمة إلى failed
     */
    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'completed_at' => now(),
        ]);
    }
}
