<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedDashboard extends Model
{
    protected $fillable = ['slug', 'usernames'];
}
