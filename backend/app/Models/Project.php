<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['title', 'description', 'thumbnail_path', 'live_demo_link', 'repository_link'];

    // Relasi Many-to-Many ke Skills
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'project_skill');
    }
}
