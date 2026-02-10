<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Project extends Model
{
    protected $fillable = ['title', 'description', 'thumbnail_path', 'live_demo_link', 'repository_link'];

    // Otomatis tambahkan thumbnail_url ke JSON
    protected $appends = ['thumbnail_url'];

    // Relasi Many-to-Many ke Skills
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'project_skill');
    }

    // Accessor untuk thumbnail_url
    protected function getThumbnailUrlAttribute()
    {
        return $this->thumbnail_path ? Storage::url($this->thumbnail_path) : null;
    }
}
