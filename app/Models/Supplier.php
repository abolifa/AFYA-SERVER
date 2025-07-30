<?php

namespace App\Models;

use App\Models\Traits\BlamesUser;
use Database\Factories\SupplierFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static inRandomOrder()
 */
class Supplier extends Model
{
    /** @use HasFactory<SupplierFactory> */
    use HasFactory, SoftDeletes, BlamesUser;

    protected $guarded = ['id'];

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
