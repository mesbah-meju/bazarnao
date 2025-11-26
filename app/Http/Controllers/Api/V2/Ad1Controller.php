<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\SliderCollection;

class Ad1Controller extends Controller
{
    public function index()
    {
        return new SliderCollection(json_decode(get_setting('home_banner1_images'), true));
    }
}
