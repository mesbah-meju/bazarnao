<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerServiceOrder extends Model
{
    protected $table = 'customer_service_order';
    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
