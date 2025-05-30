<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'slug',
        'views',
        'rating',
        'is_published',
        'published_at',
        'reading_time',
        'tags',
        'full_content',
        'user_id'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
        'rating' => 'decimal:2',
        'tags' => 'json',
        'reading_time' => 'datetime'
    ];

    // Kapcsolat a users táblával (egy post egy userhez tartozik)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
