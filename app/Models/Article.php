<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_id', 'source_article_id', 'title', 'description', 'content', 'url', 'url_to_image', 'published_at', 'author_name', 'raw', 'category', 'is_active'
    ];

    protected $casts = [
        'raw' => 'array',
        'published_at' => 'datetime'
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }
}
