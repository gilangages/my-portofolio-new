<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['title', 'description', 'icon', 'price', 'cta_link', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
