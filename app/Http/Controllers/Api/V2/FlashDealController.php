<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\FlashDealCollection;
use App\Http\Resources\V2\ProductCollection;
use App\Http\Resources\V2\ProductMiniCollection;
use App\Http\Resources\V2\FlashProductMiniCollection;
use App\Models\FlashDeal;
use App\Models\Product;
use App\Models\FlashDealProduct;
use Carbon\Carbon;


class FlashDealController extends Controller
{
    public function index()
    {
        $flash_deals = FlashDeal::where('status', 1)->where('start_date', '<=', strtotime(date('d-m-Y')))->where('end_date', '>=', strtotime(date('d-m-Y')))->get();
        return new FlashDealCollection($flash_deals);
    }

    public function happy_hour()
    {
        $flash_deals = FlashDeal::where('status', 1)->where('end_date', '>=', strtotime(date('d-m-Y')))->first();
        $flash_deals->start_date = Carbon::createFromTimestamp($flash_deals->start_date)->toDateString();
        $current_date = date('Y-m-d');
        if($flash_deals->start_date == $current_date)
        {
            return new FlashDealCollection($flash_deals);
        }
        else
        {
            return response()->json([
                'result' => false,
                'message' => 'No happy Hour',
            ]);
        }
    }

    public function products($id)
    {
        $flash_deal = FlashDeal::find($id);
        $products = collect();
        foreach ($flash_deal->flashDealProducts as $key => $flash_deal_product) 
        {
            if(Product::find($flash_deal_product->product_id) != null)
            {
                $products->push(Product::find($flash_deal_product->product_id));
            }
        }
        $productCollection = new FlashProductMiniCollection($products);
        return new FlashProductMiniCollection($products);
    }
}
