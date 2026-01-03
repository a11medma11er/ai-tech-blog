<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    // Fillable fields for mass assignment
    protected $fillable = [
        'title',
        'slug',
        'content',
        'featured_image',
        'source_url',
        'gallery',
        'is_published',
        'published_at',
        'category_id',
    ];

    protected $casts = [
        'gallery' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    // Relationship: Post belongs to Category
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getRouteKeyName(): string
{
    return 'slug';
}
}