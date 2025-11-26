<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dailyactivity extends Model
{
    protected $table = 'dailyactivities';
    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
