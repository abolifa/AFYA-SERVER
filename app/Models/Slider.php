<?php

namespace App\Models;

use Database\Factories\SliderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static count()
 */
class Slider extends Model
{
    /** @use HasFactory<SliderFactory> */
    use HasFactory;

    protected $fillable = [
        'type',
        'image',
        'post_id',
        'url',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
