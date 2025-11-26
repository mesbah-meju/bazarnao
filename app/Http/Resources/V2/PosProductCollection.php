<?php

namespace App\Http\Resources\V2;
use Auth;
use App\Models\Warehouse;
use App\Models\ProductStock;
use App\Models\Product;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PosProductCollection extends ResourceCollection
{
    public function toArray($request)
    {
        // Retrieve warehouse IDs associated with the authenticated user
        $warehouseIds = getWearhouseBuUserId(auth()->user()->id);
    
        // Pre-fetch product stocks for the given product IDs and warehouse IDs to avoid querying inside the loop
        $productStocks = ProductStock::whereIn('wearhouse_id', $warehouseIds)
                                     ->whereIn('product_id', $this->collection->pluck('id'))
                                     ->get()
                                     ->keyBy(function ($item) {
                                         return $item['product_id'] . '-' . $item['wearhouse_id'];
                                     });
    
        return [
            'data' => $this->collection->map(function ($data) use ($productStocks, $warehouseIds) {
                $product = Product::where('id', $data->id)->first();
                $pos_price = null;
                
                foreach ($warehouseIds as $warehouseId) {
                    $key = $data->id . '-' . $warehouseId;
                    if (isset($productStocks[$key])) {
                        $pos_price = $productStocks[$key]->price;
                        $discount_type_column = 'warehouse' . $warehouseId . '_discount_type';
                        $discount_column = 'warehouse' . $warehouseId . '_discount';
                        $discount_type = $product->$discount_type_column;
                        $discount = $product->$discount_column;
                
                        if ($discount_type == 'percent') {
                            $pos_price -= ($pos_price * $discount) / 100;
                        } elseif ($discount_type == 'amount') {
                            $pos_price -= $discount;
                        }
                        break;
                    }
                }
                
                return [
                    'id' => $data->id,
                    'name' => $data->name,
                    'variant_product' => $data->variant_product,
                    'thumbnail_image' => uploaded_asset($data->thumbnail_img),
                    'price' => single_price($pos_price),
                    'barcode' => $product->barcode
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
