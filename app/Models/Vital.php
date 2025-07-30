<?php

namespace App\Models;

use App\Models\Traits\BlamesUser;
use Database\Factories\VitalFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static create(array $array)
 * @method static where(string $string, mixed $id)
 */
class Vital extends Model
{
    /** @use HasFactory<VitalFactory> */
    use HasFactory, SoftDeletes, BlamesUser;

    protected $guarded = ['id'];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
