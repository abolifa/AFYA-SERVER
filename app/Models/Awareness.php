<?php

namespace App\Models;

use Database\Factories\AwarenessFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static whereRaw(string $string, array $array)
 * @method static count()
 */
class Awareness extends Model
{
    /** @use HasFactory<AwarenessFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'attachments',
        'active',
    ];
    protected $casts = [
        'attachments' => 'array',
        'active' => 'boolean',
    ];
}
