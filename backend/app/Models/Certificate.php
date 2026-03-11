<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Certificate extends Model
{
    const TYPES = ['course', 'seminar', 'webinar', 'workshop', 'bootcamp', 'competition'];

    protected $fillable = ['title', 'issuer', 'description', 'is_featured', 'start_date', 'end_date', 'type', 'image_path', 'credential_link'];

    protected $casts = [
        'is_featured' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Otomatis tambahkan image_url ke output JSON
    protected $appends = ['image_url'];

    protected function getImageUrlAttribute()
    {
        return $this->image_path ? Storage::url($this->image_path) : null;
    }
}
