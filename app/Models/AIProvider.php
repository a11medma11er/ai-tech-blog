<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class AIProvider extends Model
{
    use HasFactory;

    protected $table = 'ai_providers';

    protected $fillable = [
        'name',
        'type',
        'api_key',
        'model',
        'base_url',
        'settings',
        'is_active',
        'is_default',
        'priority',
        'description',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'priority' => 'integer',
        'api_key' => 'encrypted', // تشفير تلقائي
    ];

    /**
     * Get the active providers sorted by priority.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('priority', 'desc');
    }

    /**
     * Get the default provider.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Boot method to handle default provider logic.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->is_default) {
                // إلغاء الافتراضي عن البقية
                static::where('id', '!=', $model->id)->update(['is_default' => false]);
            }
        });
    }
}
