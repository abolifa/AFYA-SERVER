<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 */
class Setting extends Model
{
    protected $fillable = [
        'about_title',
        'about_content',
        'privacy_policy_title',
        'privacy_policy_content',
        'terms_of_service_title',
        'terms_of_service_content',
        'faq_title',
        'faq_content',
        'faq',
        'contact',
    ];

    protected $casts = [
        'faq' => 'array',
        'contact' => 'array',
    ];
}
