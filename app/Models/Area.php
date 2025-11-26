<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App;

class Area extends Model
{
    public function wearhouse(){
    	return $this->belongsTo(Warehouse::class);
    }
   
}
