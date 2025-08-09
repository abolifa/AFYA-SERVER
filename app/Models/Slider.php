<?php

namespace App\Models;

use Database\Factories\SliderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Slider extends Model
{
    /** @use HasFactory<SliderFactory> */
    use HasFactory;

    protected $fillable = [
        'type',
        'post_id',
        'image',
        'url',
        'active',
    ];

    
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
