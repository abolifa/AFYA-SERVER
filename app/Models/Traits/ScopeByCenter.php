<?php

namespace App\Models\Traits;

use App\Models\Scopes\ByCenterScope;

/**
 * @method static addGlobalScope(ByCenterScope $param)
 */
trait ScopeByCenter
{
    /**
     * Bootstrap the scope on the model.
     */
    public static function bootScopeByCenter(): void
    {
        static::addGlobalScope(new ByCenterScope);
    }

    /**
     * Allow querying for a specific center (or bypassing the global scope).
     */
    public function scopeWithCenter($query, ?int $centerId = null)
    {
        $centerId = $centerId ?? auth()->user()->center_id;

        return $query
            ->withoutGlobalScope(ByCenterScope::class)
            ->where('center_id', $centerId);
    }
}
