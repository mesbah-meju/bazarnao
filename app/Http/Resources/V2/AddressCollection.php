<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AddressCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
               return [
                    'id'      => $data->id,
                    'user_id' => $data->user_id,
                    'address' => !empty($data->address) ? $data->address : '',
                    'country' => !empty($data->country) ? $data->country : '',
                    'city' => !empty($data->city) ? $data->city : '',
                    'postal_code' => !empty($data->postal_code) ? $data->postal_code : '',
                    'phone' => !empty($data->phone) ? $data->phone : '',
                    'set_default' => $data->set_default
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
