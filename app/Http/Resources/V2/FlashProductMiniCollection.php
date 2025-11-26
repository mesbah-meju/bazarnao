<?php

namespace App\Http\Resources\V2;
use App\Models\FlashDealProduct;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FlashProductMiniCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                $flash_deal_products = FlashDealProduct::where('flash_deal_products.product_id',$data->id)
                ->first();
                if (isset($flash_deal_products->app_quantity) && $flash_deal_products->app_quantity !== '') {
                    $app_max_qty = $flash_deal_products->app_quantity;
                } else {
                    $app_max_qty = 0;
                }
                $numprice = homeDiscountedBasePrice($data->id);
                $price = (double) preg_replace("/[^0-9.]/", "", $numprice);

                return [
                    'id' => $data->id,
                    'name' => $data->name,
                    'thumbnail_image' => api_asset($data->thumbnail_img),
                    'has_discount' => homeBasePrice($data->id) != $price ,
                    'outofstock' => $data->outofstock,
                    'base_price' => format_price(homeBasePrice($data->id)-$data->discount) ,
                    'app_price' => $data->unit_price ,
                    'web_price' => $data->unit_price ,
                    'rating' => (double) $data->rating,
                    'sales' => (integer) $data->num_of_sale,
                    'discount' => !empty($data->app_discount) ? $data->app_discount : 0.00,
                    'price' => homeBasePrice($data->id),
                    'discount_price' => homeDiscountedBasePrice($data->id),
                    'max_qty' => $app_max_qty,
					'app_max_qty' => $app_max_qty,
                    'links' => [
                        'details' => route('products.show', $data->id),
                    ]
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