<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\Product;

class HappyHourProductCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                $product = Product::where('id', $data->product_id)->first();
                return [
                    'id' => $data->product_id, 
                    'name' => $product->name, 
                    'image' => uploaded_asset($product->thumbnail_img), 
                    'price' => homeDiscountedBasePrice($data->product_id),
                    'discount_type' => $data->discount_type,
                    'discount' => $data->discount,
                    'links' => [
                        'details' => route('products.show', $data->product_id), 
                    ]
                ];
            })
        ];
    }
}
