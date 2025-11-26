<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashTransfer extends Model
{
    public function wearhouse(){
    	return $this->belongsTo(Warehouse::class);
    }
    
    public function product(){
    	return $this->belongsTo(Product::class);
    }
   
}
