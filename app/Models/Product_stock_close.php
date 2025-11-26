<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class Product_stock_close extends Model
{
    protected $table = 'product_stock_close';
	protected $fillable = [
        'wh_id'
        ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    
}
