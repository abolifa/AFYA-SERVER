<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ByCenterScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        if (!$user
            || $user->can_see_other_records
            || is_null($user->center_id)
        ) {
            return;
        }

        $builder->where(
            $model->getTable() . '.center_id',
            $user->center_id
        );
    }
}
