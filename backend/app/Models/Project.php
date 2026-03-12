<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Project extends Model
{
    const STATUSES = ['completed', 'in_development', 'on_hold', 'cancelled'];
    const TYPES = ['web_development', 'mobile_development', 'desktop_application', 'game_development'];

    protected $fillable = ['title', 'description', 'is_featured', 'start_date', 'end_date', 'status', 'type', 'thumbnail_path', 'live_demo_link', 'repository_link', 'team_size', 'role'];

    protected $casts = [
        'is_featured' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

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
