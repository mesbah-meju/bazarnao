<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\HappyHourCollection;
use App\Http\Resources\V2\HappyHourProductMiniCollection;
use App\Models\HappyHour;
use App\Models\Product;
use Carbon\Carbon;

class HappyHourController extends Controller
{
    public function index()
    {
        $happy_hours = HappyHour::where('status', 1)
                                ->where('start_date', '<=', strtotime(date('d-m-Y')))
                                ->where('end_date', '>=', strtotime(date('d-m-Y')))
                                ->get();
    
        if ($happy_hours->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No active Happy Hours found.',
                'status' => 404,
            ], 404);
        }
    
        return new HappyHourCollection($happy_hours);
    }
    

    public function products($id)
    {
        $happy_hour = HappyHour::with('happy_hour_products')->find($id);
    
        if (!$happy_hour) {
            return response()->json([
                'success' => false,
                'message' => 'Happy Hour not found!',
                'status' => 404,
            ], 404);
        }
    
        $current_time = strtotime(date('d-m-Y'));
        if ($happy_hour->status != 1 || $happy_hour->start_date > $current_time || $happy_hour->end_date < $current_time) {
            return response()->json([
                'success' => false,
                'message' => 'Happy Hour is not active.',
                'status' => 400,
            ], 400);
        }
    
        $happy_hour_products = $happy_hour->happy_hour_products;
    
        $products = collect();
        foreach ($happy_hour_products as $happy_hour_product) {
            $product = Product::find($happy_hour_product->product_id);
            if ($product) {
                $products->push($product);
            }
        }
    
        return new HappyHourProductMiniCollection($products);
    }
    
    
}
