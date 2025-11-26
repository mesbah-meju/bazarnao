<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\Review;
use App\Models\Attribute;
use App\Models\Product;
use App\Models\Group_product;

class ProductDetailCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                $numprice = MainhomeDiscountedBasePrice($data->id);
                $price = (double) preg_replace("/[^0-9.]/", "", $numprice);
                       
                // Check if the product is a group product
                $isGroupProduct = $data->is_group_product;
                $groupProductsDetails = [];
                $totalPreviousPrice = 0;
                $totalDiscount = 0;
        
                if ($isGroupProduct) {
                    // Fetch group products details
                        $groupProducts = Product::join('group_products', 'products.id', '=', 'group_products.group_product_id')
                                                        ->select('group_products.*')
                                                        ->where('products.id', $data->id)
                                                        ->get();
                                                        
                        $groupProductsDetails = $groupProducts->map(function($groupProduct) use (&$totalPreviousPrice, &$totalDiscount) {
                            $product = Product::find($groupProduct->product_id);
                            $previousPrice = $product->unit_price * $groupProduct->app_qty;
                            $newUnitPrice = $groupProduct->app_price/$groupProduct->app_qty;
                            $newTotalPrice = $groupProduct->app_price;
                            $discountAmount = $previousPrice - $newTotalPrice;
                            $totalPreviousPrice += $previousPrice;
                            $totalDiscount += $discountAmount;
            
                            return [
                                'id' => $product->id,
                                'name' => $product->name,
                                'unit_price' => $product->unit_price,
                                'quantity' => $groupProduct->app_qty,
                                'new_unit_price' => $newUnitPrice,
                                'discount_amount' => $groupProduct->app_discount_amount,
                                'discount_type' => $groupProduct->app_discount_type
                            ];
                        });
                }
                
                // dd($data->user->shop);
                return [
                    'id' => (integer) $data->id,
                    'name' => $data->name,
                    'added_by' => $data->added_by,
                    'seller_id' => $data->user->id,
                    'shop_id' => $data->added_by == 'admin' ? 0 : ($data->user && $data->user->shop ? $data->user->shop->id : null),
                    'shop_name' => $data->added_by == 'admin' ? 'In House Product' : ($data->user && $data->user->shop ? $data->user->shop->name : 'No Shop'),
                    'shop_logo' => $data->added_by == 'admin' ? api_asset(get_setting('header_logo')) : ($data->user && $data->user->shop ? api_asset($data->user->shop->logo) : " "),
                    'photos' => get_images_path($data->photos),
                    'thumbnail_image' => api_asset($data->thumbnail_img),
                    'tags' => explode(',', $data->tags),
                    'price_high_low' => (double) explode('-', mainhomeDiscountedPrice($data->id))[0] == (double) explode('-', mainhomeDiscountedPrice($data->id))[1] ? format_price((double) explode('-', mainhomeDiscountedPrice($data->id))[0]) : "From " . format_price((double)explode('-', mainhomeDiscountedPrice($data->id))[0]) . " to " . format_price((double) explode('-', mainhomeDiscountedPrice($data->id))[1]),
                    'choice_options' => $this->convertToChoiceOptions(json_decode($data->choice_options)),
                    'colors' => json_decode($data->colors),
                    'has_discount' => homeBasePrice($data->id) != $price,
                    'stroked_price' => home_base_price($data->id),
                    'main_price' => main_home_discounted_base_price($data->id),
                    'calculable_price' => (double) MainhomeDiscountedBasePrice($data->id),
                    'currency_symbol' => currency_symbol(),
                    'current_stock' => (integer) $data->current_stock,
                    'unit' => $data->unit,
                    'rating' => (double) $data->rating,
                    'rating_count' => (integer) Review::where(['product_id' => $data->id])->count(),
                    'earn_point' => (double) $data->earn_point,
                    'outofstock' => (double) $data->outofstock,
                    'description' => $data->description,
                    'max_qty' => $data->app_max_qty,
                    'is_group_product' => $groupProductsDetails,
                    'total_previous_price' => (string)$totalPreviousPrice,
                    'total_discount' => (string)$totalDiscount
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

    protected function convertToChoiceOptions($data){
        $result = array();
        foreach ($data as $key => $choice) {
            $item['name'] = $choice->attribute_id;
            $item['title'] = Attribute::find($choice->attribute_id)->name;
            $item['options'] = $choice->values;
            array_push($result, $item);
        }
        return $result;
    }

    protected function convertPhotos($data){
        $result = array();
        foreach ($data as $key => $item) {
            array_push($result, api_asset($item));
        }
        return $result;
    }
}
