<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static count()
 */
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'main_image',
        'other_images',
        'tags',
        'active',
    ];

    protected $casts = [
        'other_images' => 'array',
        'tags' => 'array',
        'active' => 'boolean',
    ];

    public function sliders(): HasMany
    {
        return $this->hasMany(Slider::class);
    }
}
