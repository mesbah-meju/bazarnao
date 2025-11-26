<?php

namespace App\Http\Resources\V2;

use App\Models\RefundRequest;


use Illuminate\Http\Resources\Json\ResourceCollection;

class PurchaseHistoryItemsCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {

                $Refund = RefundRequest::where('refund_requests.order_detail_id',$data->id)->first();
    
                return [
                    'product_id' => $data->product->id,
                    'product_name' => $data->product->name,
                    'order_details_id' => $data->id,
                    'variation' => $data->variation,
                    'price' => format_price($data->price),
                    'tax' => format_price($data->tax),
                    'shipping_cost' =>format_price($data->shipping_cost),
                    'coupon_discount' => format_price($data->coupon_discount),
                    'quantity' => $data->quantity,
                    'payment_status' => $data->payment_status,
                    'payment_status_string' => ucwords(str_replace('_', ' ', $data->payment_status)),
                    'delivery_status' => $data->delivery_status,
                    'delivered_date' => $data->delivered_date,
                    'Refund_status' => $Refund?'Refund_Requested' : 'Not Refund Requested',
                    'product_refundable_status' => $data->product->refundable,
                    'delivery_status_string' => $data->delivery_status == 'pending'? "Order Placed" : ucwords(str_replace('_', ' ',  $data->delivery_status)),
                ];
            })
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
