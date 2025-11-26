<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\SliderCollection;

class Ad2Controller extends Controller
{
    public function index()
    {
        return new SliderCollection(json_decode(get_setting('home_banner2_images'), true));
    }
}
