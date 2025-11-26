<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = [
        'order_id',
        'seller_id',
        'product_id',
        'barcode',
        'variation',
        'purchase_price',
        'price',
        'tax',
        'shipping_cost',
        'discount',
        'quantity',
        'payment_status',
        'delivery_status',
        'shipping_type',
        'pickup_point_id',
        'product_referral_code',
        'delivery_status_changer_id',
        'special_discount',
        'profit'
    ];
    
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }


    public function refund_request()
    {
        return $this->hasOne(RefundRequest::class);
    }

}
