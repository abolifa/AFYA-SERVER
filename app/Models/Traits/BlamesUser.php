<?php

namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Automatically assigns created_by, updated_by, deleted_by.
 *
 * Usage: use SoftDeletes, BlamesUsers;
 */
trait BlamesUser
{
    public static function bootBlamesUser(): void
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                if (!$model->created_by) {
                    $model->created_by = Auth::id();
                }
                if (!$model->updated_by) {
                    $model->updated_by = Auth::id();
                }
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                // Optional: skip if only deleting
                $dirty = array_keys($model->getDirty());
                if (!empty(array_diff($dirty, ['deleted_at', 'deleted_by']))) {
                    $model->updated_by = Auth::id();
                }
            }
        });

        static::deleting(function ($model) {
            if (!Auth::check()) return;

            if ($model->usesSoftDeletes()) {
                if (!$model->isForceDeleting()) {
                    $model->deleted_by = Auth::id();
                    $model->saveQuietly(); // ensure persistence
                } else {
                    // force delete: still record who (not persisted after row removal, unless you log it externally)
                    $model->deleted_by = Auth::id();
                }
            } else {
                $model->deleted_by = Auth::id();
                // Hard delete: persist if you want to catch in an audit trail first
                $model->saveQuietly();
            }
        });

        static::restoring(function ($model) {
            $model->deleted_by = null;
        });
    }

    protected function usesSoftDeletes(): bool
    {
        return in_array(
            'Illuminate\Database\Eloquent\SoftDeletes',
            class_uses_recursive($this)
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
