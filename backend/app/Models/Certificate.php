<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = ['title', 'issuer', 'description', 'image_path', 'credential_link'];
}
