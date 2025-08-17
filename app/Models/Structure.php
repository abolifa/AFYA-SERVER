<?php

namespace App\Models;

use Database\Factories\StructureFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Structure extends Model
{
    /** @use HasFactory<StructureFactory> */
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'type',
        'phone',
        'email',
        'address',
        'employees',
    ];

    protected $casts = [
        'employees' => 'array',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Structure::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Structure::class, 'parent_id');
    }
}
