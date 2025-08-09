<?php

namespace App\Models;

use Database\Factories\AwarenessFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static findOrFail($id)
 */
class Awareness extends Model
{
    /** @use HasFactory<AwarenessFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];
}
