<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = ['name', 'job_title', 'about_description', 'photo_path', 'cv_path'];
}
