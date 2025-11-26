<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// class OpeningStock extends Model
// {
// }

class OpeningStock extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'qty','price','amount', 
    ];
}
