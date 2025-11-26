<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\BannerCollection;

class BannerController extends Controller
{

    public function index()
    {
        return new BannerCollection(json_decode(get_setting('home_banner1_images'), true));
    }

    public function app_banner()
    {
        if(Session::has('locale')){
            $locale = Session::get('locale', Config::get('app.locale'));
            return new BannerCollection(json_decode(get_setting('app_banner_bangla_images'), true));
        }
        else{
            $locale = 'en';
            return new BannerCollection(json_decode(get_setting('app_banner_images'), true));
        }
    }
}