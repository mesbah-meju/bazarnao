<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HappyHourProduct extends Model
{
    protected $fillable=['happy_hour_id', 'product_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
