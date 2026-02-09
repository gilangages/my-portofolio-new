<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    // Kunci 'id' agar tidak bisa diubah sembarangan, sisanya BEBAS diisi (Mass Assignment)
    protected $guarded = ['id'];
}
