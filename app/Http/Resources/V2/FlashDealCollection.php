<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\V2\FlashDealProductCollection;
use App\Models\FlashDeal;
use App\Models\FlashDealProduct;
use App\Models\Product;

class FlashDealCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                $products = FlashDealProduct::where('flash_deal_id',$data->id)->get();
                return [
                    'id' => $data->id,
                    'title' => $data->title,
                    'start_date' => (int)$data->start_date,
                    'date' => (int) $data->end_date,
                    'discount_percent' => (int) $data->discount_percent,
                    'status' => $data->status,
                    'featured' => $data->featured,
                    'banner' => api_asset($data->banner),
                    'products' => new FlashDealProductCollection($products)
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
