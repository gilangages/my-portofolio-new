<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Certificate extends Model
{
    protected $fillable = ['title', 'issuer', 'description', 'image_path', 'credential_link'];

    // Otomatis tambahkan image_url ke output JSON
    protected $appends = ['image_url'];

    protected function getImageUrlAttribute()
    {
        return $this->image_path ? Storage::url($this->image_path) : null;
    }
}
