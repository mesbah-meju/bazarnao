<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    public function user()
    {
    return $this->belongsTo(User::class);
    }

    public function role()
    {
    return $this->belongsTo(Role::class);
    }
}
