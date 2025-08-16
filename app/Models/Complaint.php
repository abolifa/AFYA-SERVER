<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $data)
 */
class Complaint extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'message',
    ];
}
