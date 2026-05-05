<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageSection extends Model
{
    protected $fillable = [
        'key',
        'name',
        'is_enabled',
        'position',
        'layout',
        'settings',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'position' => 'integer',
        'settings' => 'array',
    ];
}
