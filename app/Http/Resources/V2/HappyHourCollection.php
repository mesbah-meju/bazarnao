<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\V2\HappyHourProductCollection;
use App\Models\HappyHourProduct;

class HappyHourCollection extends ResourceCollection
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
                $products = HappyHourProduct::where('happy_hour_id', $data->id)->get();
                
                return [
                    'id' => $data->id,
                    'title' => $data->title,
                    'start_date' => (int) $data->start_date,
                    'end_date' => (int) $data->end_date,
                    'status' => $data->status,
                    'featured' => $data->featured,
                    'banner' => api_asset($data->banner), 
                    'products' => new HappyHourProductCollection($products) 
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
