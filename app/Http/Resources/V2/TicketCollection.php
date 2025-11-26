<?php

namespace App\Http\Resources\V2;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TicketCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                return [
                    'code' => (string)$data->code,
					'subject' =>$data->subject,
					'details' =>$data->details,
					'status' =>$data->status,
                    'date' => Carbon::createFromTimestamp(strtotime($data->created_at))->format('d-m-Y'),
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
