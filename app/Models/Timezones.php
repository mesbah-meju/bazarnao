<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timezones  extends Model{
    public static function timezonesToArray() {
        return [
            'UTC' => 'UTC',
            'America/New_York' => 'America/New_York',
            'Europe/London' => 'Europe/London',
            'Asia/Dhaka' => 'Asia/Dhaka',
        ];
    }
    
}
