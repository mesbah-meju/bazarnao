<?php

namespace App\Http\Resources\V2;

use App\Models\HappyHourProduct;
use Illuminate\Http\Resources\Json\ResourceCollection;

class HappyHourProductMiniCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                $happy_hour_product = HappyHourProduct::where('happy_hour_products.product_id', $data->id)
                    ->first();

                if (isset($happy_hour_product->app_quantity) && $happy_hour_product->app_quantity !== '') {
                    $app_max_qty = $happy_hour_product->app_quantity;
                } else {
                    $app_max_qty = 0;
                }

                $numprice = homeDiscountedBasePrice($data->id);
                $price = (double) preg_replace("/[^0-9.]/", "", $numprice);

                return [
                    'id' => $data->id,
                    'name' => $data->name,
                    'thumbnail_image' => api_asset($data->thumbnail_img),
                    'has_discount' => homeBasePrice($data->id) != $price,
                    'outofstock' => $data->outofstock,
                    'base_price' => format_price(homeBasePrice($data->id) - $data->discount),
                    'app_price' => $data->unit_price,
                    'web_price' => $data->unit_price,
                    'rating' => (double) $data->rating,
                    'sales' => (integer) $data->num_of_sale,
                    'discount_type' => $happy_hour_product->discount_type,
                    // 'discount' => !empty($data->app_discount) ? $data->app_discount : 0.00,
                    'discount' => $happy_hour_product->discount,
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
