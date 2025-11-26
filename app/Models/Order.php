<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'coupon_id',
        'guest_id',
        'shipping_address',
        'payment_type',
        'payment_status',
        'payment_details',
        'warehouse',
        'cash_collection',
        'reason_of_cancel',
        'cancel_user_id',
        'grand_total',
        'coupon_discount',
        'special_discount',
        'code',
        'date',
        'purchase_price',
        'viewed',
        'delivery_viewed',
        'delivery_boy',
        'payment_status_viewed',
        'commission_calculated',
        'user_ip',
        'payment_status_changer_id',
        'order_from',
        'confirmed_by',
        'confirm_date',
        'canceled_by',
        'cancel_date',
        'on_delivery_by',
        'on_delivery_date',
        'delivered_by',
        'delivered_date',
        'is_deduct',
        'change_amount',
        'received_amount',
        'online_order_delivery_status',
        'shipment_cost'
    ];
    
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function refund_requests()
    {
        return $this->hasMany(RefundRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class,'customer_id');
    }

    public function club_point()
    {
        return $this->hasMany(ClubPoint::class);
    }
}
