<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryExecutiveLedger extends Model
{
    protected $table = 'delivery_executive_ledger';
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function user(){
    	return $this->belongsTo(User::class);
    }
}
