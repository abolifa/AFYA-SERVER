<?php

namespace App\Models;

use Database\Factories\CounterFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    /** @use HasFactory<CounterFactory> */
    use HasFactory;

    protected $fillable = [
        'title', 'slogan', 'color', 'type', 'model', 'fixed_value'
    ];

    protected $appends = ['value'];

    public function getValueAttribute()
    {
        if ($this->type === 'model_count' && class_exists($this->model)) {
            $modelClass = $this->model;
            return $modelClass::count();
        }
        return $this->fixed_value ?? 0;
    }
}
