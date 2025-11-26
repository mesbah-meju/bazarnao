<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App;

class OrderStatusLog extends Model
{
    public function user(){
    	return $this->belongsTo(User::class);
    }
   
}
