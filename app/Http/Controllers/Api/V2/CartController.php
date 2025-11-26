<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\Cart;
use App\Models\FlashDeal;
use App\Models\FlashDealProduct;
use App\Models\Product;
use App\Models\Group_product;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Offer;
use App\Models\Order;

class CartController extends Controller
{
    public function summary($user_id, $owner_id)
    {
        $items = Cart::where('user_id', $user_id)->get();

        if ($items->isEmpty()) {
            return response()->json([
                'sub_total' => format_price(0.00),
                'tax' => format_price(0.00),
                'shipping_cost' => format_price(0.00),
                'discount' => format_price(0.00),
                'grand_total' => format_price(0.00),
                'grand_total_value' => 0.00,
                'coupon_amount' => 0.00,
                'coupon_code' => "",
                'coupon_applied' => false,
                'items' => array(),
            ]);
        }

        $sum = 0.00;
        $disc = 0.00;
        $specialdiscount = 0.00;
        foreach ($items as $cartItem) {
            $item_sum = 0;
            $item_sum += ($cartItem->price + $cartItem->tax) * $cartItem->quantity;
            $item_sum += ($cartItem->discount * $cartItem->quantity);
            $item_sum += $cartItem->shipping_cost;
            $sum +=  $item_sum;   //// 'grand_total' => $request->g
            $disc += $cartItem->discount * $cartItem->quantity;
            $specialdiscount += $cartItem->special_discount;
        }
        $coupon_amount = 0;
        if (!empty($items)) {
            $sum -=  $items[0]->coupon_amount;
            //$sum -=  $items[0]->special_discount;
            $coupon_amount = $items[0]->coupon_amount;
        }
        $disc1 = $disc + $specialdiscount;

        $shipping_skip_total = \App\Models\BusinessSetting::where('type', 'flat_rate_shipping_cost_total')->first()->value;
        if ($shipping_skip_total <= ($sum + $coupon_amount)) {
            $calculate_shipping = 0;
        } else {
            $calculate_shipping = \App\Models\BusinessSetting::where('type', 'flat_rate_shipping_cost')->first()->value;
        }

        return response()->json([
            'sub_total' => format_price($sum + $coupon_amount),
            'tax' => format_price($items->sum('tax')),
            'shipping_cost' => format_price($calculate_shipping),
            'discount' => format_price($disc1),
            'grand_total' => format_price($sum + $calculate_shipping - $disc1),
            'grand_total_value' => convert_price($sum + $calculate_shipping - $disc1),
            'coupon_code' => $items[0]->coupon_code,
            'coupon_amount' => format_price($items[0]->coupon_amount),
            'coupon_applied' => $items[0]->coupon_applied == 1,
            'items' => $this->getList($user_id),
        ]);
    }

    public function getList($user_id)
    {
        $owner_ids = Cart::where('user_id', $user_id)->select('owner_id')->groupBy('owner_id')->pluck('owner_id')->toArray();

        $currency_symbol = currency_symbol();
        $shops = [];
        if (!empty($owner_ids)) {
            foreach ($owner_ids as $owner_id) {
                $shop = array();
                $shop_items_raw_data = Cart::where('user_id', $user_id)->get()->toArray();
                $shop_items_data = array();
                if (!empty($shop_items_raw_data)) {
                    foreach ($shop_items_raw_data as $shop_items_raw_data_item) {
                        $product = Product::where('id', $shop_items_raw_data_item["product_id"])->first();
                        $shop_items_data_item["id"] = $shop_items_raw_data_item["id"];
                        $shop_items_data_item["owner_id"] = $shop_items_raw_data_item["owner_id"];
                        $shop_items_data_item["user_id"] = $shop_items_raw_data_item["user_id"];
                        $shop_items_data_item["product_id"] = $shop_items_raw_data_item["product_id"];
                        $shop_items_data_item["name"] = $product->name;
                        $shop_items_data_item["thumbnail_image"] = api_asset($product->thumbnail_img);
                        $shop_items_data_item["variation"] = $shop_items_raw_data_item["variation"];
                        $shop_items_data_item["price"] = number_format($shop_items_raw_data_item['price'], 2, '.', '');
                        $shop_items_data_item["base_price"] = single_price($shop_items_raw_data_item["price"] + $shop_items_raw_data_item["discount"]);
                        $shop_items_data_item["currency_symbol"] = $currency_symbol;
                        $shop_items_data_item["discount"] = $shop_items_raw_data_item["discount"];
                        $shop_items_data_item["tax"] = $shop_items_raw_data_item["tax"];
                        $shop_items_data_item["shipping_cost"] = $shop_items_raw_data_item["shipping_cost"];
                        $shop_items_data_item["quantity"] = $shop_items_raw_data_item["quantity"];
                        $shop_items_data_item["total_amount"] = ($shop_items_raw_data_item["price"]) * $shop_items_raw_data_item["quantity"];
                        $shop_items_data_item["lower_limit"] = 1;
                        $shop_items_data_item["upper_limit"] = $product->app_max_qty;

                        $shop_items_data[] = $shop_items_data_item;
                    }
                }


                $shop_data = Shop::where('user_id', $owner_id)->first();
                if ($shop_data) {
                    $shop['name'] = $shop_data->name;
                    $shop['owner_id'] = $owner_id;
                    $shop['cart_items'] = $shop_items_data;
                } else {
                    $shop['name'] = "Inhouse";
                    $shop['owner_id'] = 1;
                    $shop['cart_items'] = $shop_items_data;
                }
                $shops[] = $shop;
            }
        }

        //dd($shops);

        return response()->json($shops);
    }

    public function add_(Request $request)
    {

        $product = Product::findOrFail($request->id);

        $existingCartItem = Cart::where('user_id', $request->user_id)
            ->where('product_id', $request->id)
            ->first();


        if ($existingCartItem) {
            $existingCartItem->update([
                'quantity' => DB::raw('quantity + 1')
            ]);
        } else {
            Cart::create([
                'user_id' => $request->user_id,
                'product_id' => $request->id,
                'price' => $product->unit_price,
                'shipping_cost' => 0,
                'quantity' => 1,
            ]);
        }

        return response()->json([
            'result' => true,
            'message' => 'Product added to cart successfully'
        ]);
    }

    public function cartRemove(Request $request)
    {
        $user_id = $request->user_id;
        $product_id = $request->product_id;
        $carts = Cart::where('user_id', $user_id)->where('product_id', $product_id)->first();
        if ($carts->quantity > 0) {
            $carts->quantity = $carts->quantity - 1;
            if ($carts->save()) {
                return response()->json([
                    'result' => true,
                    'message' => 'Product Removed from cart successfully'
                ]);
            }
        } else {
            if ($carts->delete()) {
                return response()->json([
                    'result' => true,
                    'message' => 'Product Removed from cart successfully'
                ]);
            }
        }
        return response()->json([
            'result' => false,
            'message' => 'product not found'
        ]);
    }

    // public function add(Request $request)
    // {

    //     $product = Product::findOrFail($request->id);

    //     $variant = $request->variant;

    //     $tax = 0;

    //     $price = $product->unit_price;

    //     $flash_deals = FlashDeal::where('status', 1)->get();

    //     $inFlashDeal = false;
    //     if($request->campaign == true){
    //         foreach ($flash_deals as $flash_deal) {
    //             if ($flash_deal != null && $flash_deal->status == 1 && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first() != null) {
    //                 $flash_deal_product = FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first();
    //                 if ($flash_deal_product->discount_type == 'percent') {
    //                     $price -= ($price * $flash_deal_product->discount) / 100;
    //                 } elseif ($flash_deal_product->discount_type == 'amount') {
    //                     $price -= $flash_deal_product->discount;
    //                 }
    //                 $inFlashDeal = true;
    //                 break;
    //             }
    //         }
    //     }
    //     if (!$inFlashDeal) {
    //         if($product->is_group_product){
    //             $price = Group_product::where('group_product_id',$product->id)->sum('app_price');
    //         }else{
    //             if ($product->app_discount_type == 'percent') {
    //                 $price -= ($price * $product->app_discount) / 100;
    //             } elseif ($product->app_discount_type == 'amount') {
    //                 $price -= $product->app_discount;
    //             }
    //         }
    //     }

    //     $stock = 10;

    //     $qty = $request->quantity;

    //     $Check = Cart::where('user_id',$request->user_id)
    //         ->where('owner_id', $product->user_id)
    //         ->where('product_id',$request->id)
    //         ->value('quantity');

    //     $total = intval($qty) + $Check;


    //     if (intval($product->app_max_qty) < $total) { 
    //         Cart::updateOrCreate([
    //                 'user_id' => $request->user_id,
    //                 'owner_id' => $product->user_id,
    //                 'product_id' => $request->id,
    //                 'variation' => $variant
    //             ], [
    //                 'price' => $price,
    //                 'tax' => $tax,
    //                 'shipping_cost' => 0,
    //                 'quantity' => DB::raw("$product->app_max_qty")
    //             ]);
    //             return response()->json([
    //                 'result' => false,
    //                 'cartQty'=>Cart::where('user_id',$request->user_id)->count(),
    //                 'message' => 'You Can not add more than '.($product->app_max_qty).' Quantity for this product'
    //             ]);
    //         }
    //         else{
    //                 Cart::updateOrCreate([
    //                     'user_id' => $request->user_id,
    //                     'owner_id' => $product->user_id,
    //                     'product_id' => $request->id,
    //                     'variation' => $variant
    //                 ], [
    //                     'price' => $price,
    //                     'tax' => $tax,
    //                     'shipping_cost' => 0,
    //                     'quantity' => DB::raw("quantity + $request->quantity")
    //                 ]);

    //                 return response()->json([
    //                     'result' => true,
    //                     'cartQty'=>Cart::where('user_id',$request->user_id)->count(),
    //                     'message' => 'Product added to cart successfully'
    //                 ]);
    //     }
    // }

    public function add(Request $request)
    {
        $product = Product::findOrFail($request->id);

        $variant = $request->variant;

        $tax = 0;

        $price = $product->unit_price;

        $flash_deals = FlashDeal::where('status', 1)->get();

        $inFlashDeal = false;
        if ($request->campaign == true) {
            foreach ($flash_deals as $flash_deal) {
                if ($flash_deal != null && $flash_deal->status == 1 && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first() != null) {
                    $flash_deal_product = FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first();
                    if ($flash_deal_product->discount_type == 'percent') {
                        $price -= ($price * $flash_deal_product->discount) / 100;
                    } elseif ($flash_deal_product->discount_type == 'amount') {
                        $price -= $flash_deal_product->discount;
                    }
                    $inFlashDeal = true;
                    break;
                }
            }
        }

        if (!$inFlashDeal) {
            if ($product->is_group_product) {
                $price = Group_product::where('group_product_id', $product->id)->sum('app_price');
            } else {
                if ($product->app_discount_type == 'percent') {
                    $price -= ($price * $product->app_discount) / 100;
                } elseif ($product->app_discount_type == 'amount') {
                    $price -= $product->app_discount;
                }
            }
        }

        // Happy Hour Discount
        $happy_hour = \App\Models\HappyHour::with('happy_hour_products')
            ->where('status', 1)
            ->where('end_date', '>=', now())
            ->first();

        $happy_hour_product = $happy_hour ? $happy_hour->happy_hour_products->where('product_id', $product->id)->first() : null;
        if ($happy_hour_product) {
            $price -= ($price * $happy_hour_product->discount) / 100;
        }

        $stock = 10;

        $qty = $request->quantity;

        $Check = Cart::where('user_id', $request->user_id)
            ->where('owner_id', $product->user_id)
            ->where('product_id', $request->id)
            ->value('quantity');

        $total = intval($qty) + $Check;

        if (intval($product->app_max_qty) < $total) {
            Cart::updateOrCreate([
                'user_id' => $request->user_id,
                'owner_id' => $product->user_id,
                'product_id' => $request->id,
                'variation' => $variant
            ], [
                'price' => $price,
                'tax' => $tax,
                'shipping_cost' => 0,
                'quantity' => DB::raw("$product->app_max_qty")
            ]);
            return response()->json([
                'result' => false,
                'cartQty' => Cart::where('user_id', $request->user_id)->count(),
                'message' => 'You cannot add more than ' . ($product->app_max_qty) . ' Quantity for this product'
            ]);
        } else {
            Cart::updateOrCreate([
                'user_id' => $request->user_id,
                'owner_id' => $product->user_id,
                'product_id' => $request->id,
                'variation' => $variant
            ], [
                'price' => $price,
                'tax' => $tax,
                'shipping_cost' => 0,
                'quantity' => DB::raw("quantity + $request->quantity")
            ]);

            return response()->json([
                'result' => true,
                'cartQty' => Cart::where('user_id', $request->user_id)->count(),
                'message' => 'Product added to cart successfully'
            ]);
        }
    }


    public function addMultiple(Request $request)
    {
        Cart::truncate();
        $total = 0;
        $disc = 0;
        $err = 0;
        foreach ($request->cartData as $cdata) {
            $product = Product::findOrFail($cdata['id']);
            $qty = $cdata['quantity'];

            $variant = $request->variant;
            $tax = 0;
            $price = $product->unit_price;

            //discount calculation based on flash deal and regular discount
            //calculation of taxes
            $flash_deals = FlashDeal::where('status', 1)->get();
            $inFlashDeal = false;
            $discount = 0;
            foreach ($flash_deals as $flash_deal) {
                if ($flash_deal != null && $flash_deal->status == 1 && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first() != null) {
                    $flash_deal_product = FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first();
                    if ($flash_deal_product->discount_type == 'percent') {
                        $discount = ($price * $flash_deal_product->discount) / 100;
                        $price -= ($price * $flash_deal_product->discount) / 100;
                    } elseif ($flash_deal_product->discount_type == 'amount') {
                        $discount = $flash_deal_product->discount;
                        $price -= $flash_deal_product->discount;
                    }
                    $inFlashDeal = true;
                    break;
                }
            }
            if (!$inFlashDeal) {
                if ($product->app_discount_type == 'percent') {
                    $discount = ($price * $product->app_discount) / 100;
                    $price -= ($price * $product->app_discount) / 100;
                } elseif ($product->app_discount_type == 'amount') {
                    $discount = $product->app_discount;
                    $price -= $product->app_discount;
                }
            }

            $stock = 10; //$product->stocks->where('variant', $variant)->first()->qty;

            if (($product->app_max_qty) <= $qty) {
                $err++;
                $qty = $product->app_max_qty;
            }
            $total += $price * $qty;
            $disc += $discount * $qty;
            Cart::updateOrCreate([
                'user_id' => $request->user_id,
                'owner_id' => $product->user_id,
                'product_id' => $cdata['id'],
                'variation' => $variant,
                'discount' => $discount
            ], [
                'price' => $price,
                'tax' => $tax,
                'shipping_cost' => 0,
                'quantity' => DB::raw("quantity + $qty")
            ]);
        }
        $dis = $this->calculate_discount($total, $request->user_id, $product->user_id, $disc);
        return response()->json([
            'result' => true,
            'owner_id' => $product->user_id,
            'cartQty' => Cart::where('user_id', $request->user_id)->count(),
            'message' => ($err > 0) ? 'Product updated to cart successfully' : 'Product added to cart successfully'
        ]);
    }

    function calculate_discount($total, $user_id, $owner_id, $dd)
    {
        $total1 = $total;
        $offer_arr = array();
        $offers = Offer::get();
        $dis = 0;
        $products = array();
        foreach ($offers as $offer) {
            if (time() >= $offer->start_date || time() <= $offer->end_date) {
                $d = json_decode($offer->details);

                if (strpos($offer->title, 'Bkash') !== false) {
                    continue;
                }

                if (strpos($offer->title, '2nd') !== false) {
                    $uid = $user_id;
                    $orderCount = Order::leftJoin('order_details', 'orders.id', '=', 'order_details.order_id')->where('orders.user_id', $uid)->whereNotIn('order_details.delivery_status', ['cancel'])->whereBetween('orders.created_at', [date('Y-m-01 00:00:00'), date('Y-m-t 23:59:59')])->groupBy('orders.id')->count();
                    if ($orderCount == 1) {
                        if (($total >= $d[0]->min_buy && $total <= $d[0]->max_discount)) {
                            foreach ($d as $row) {
                                $this->addToDiscountProduct($row->product_id, $user_id);
                            }
                        }
                    }
                } else {
                    if ($offer->type == 'cart_base') {
                        if (!empty($d->product_id)) {
                            foreach ($d->product_id as $pd_id) {
                                foreach (Cart::where('user_id', $user_id)->where('owner_id', $owner_id)->get() as $key => $cartItem) {
                                    if ($pd_id == $cartItem->product_id) {
                                        $total -= ($cartItem['price'] * $cartItem['quantity']);
                                    }
                                }
                            }
                        }

                        if (($total >= $d->min_buy && $total <= $d->max_discount)) {
                            $dis = $dis1 = $offer->discount;
                            $type = $offer->discount_type;
                            if ($type == 'percent') {
                                $dis1 = (($total) * $offer->discount) / 100;
                            }
                            $cart = Cart::where('user_id', $user_id)->where('owner_id', $owner_id)->first();
                            $d = $cart->special_discount;
                            $cart->update(['special_discount' => ($dis1 + $d)]);
                        }
                    } else {
                        if (($total >= $d[0]->min_buy && $total <= $d[0]->max_discount)) {
                            $dis = $offer->discount;
                            foreach ($d as $row) {
                                if ($offer->full_discount == 0) {
                                    foreach (Cart::where('user_id', $user_id)->where('owner_id', $owner_id)->get() as $key => $cartItem) {
                                        if ($row->product_id == $cartItem['product_id']) {

                                            $max_qty = $offer->max_qty;
                                            $itm_disc = $offer->disc_per_qty;
                                            if ($cartItem['quantity'] > $max_qty) {
                                                $qqty = $max_qty;
                                            } else {
                                                $qqty = $cartItem['quantity'];
                                            }
                                            $cartItem['discount'] = $itm_disc * $qqty;
                                            $dis += $itm_disc * $qqty;
                                        }
                                    }
                                    $cart = Cart::where('user_id', $user_id)->where('owner_id', $owner_id)->first();
                                    $d = $cart->special_discount;
                                    $cart->update(['special_discount' => ($dis + $d)]);
                                } else {
                                    $this->addToDiscountProduct($row->product_id, $user_id);
                                }
                            }
                        }
                    }
                }
            }
        }
        return $total1 - $dis;
    }

    function addToDiscountProduct($p_id, $user_id)
    {
        $product = Product::find($p_id);
        Cart::updateOrCreate([
            'user_id' => $user_id,
            'owner_id' => $product->user_id,
            'product_id' => $p_id,
            'variation' => '',
            'discount' => 0
        ], [
            'price' => 0,
            'tax' => 0,
            'shipping_cost' => 0,
            'quantity' => DB::raw("quantity + 1")
        ]);
    }

    public function changeQuantity(Request $request)
    {
        $cart = Cart::find($request->id);
        if ($cart != null) {
            $product = Product::findOrFail($cart['product_id']);

            $cart->update([
                'quantity' => $request->quantity,
                'discount' => $product->app_discount * $request->quantity
            ]);

            return response()->json(['result' => true, 'message' => 'Cart updated'], 200);
        }

        return response()->json(['result' => false, 'message' => 'Something went wrong'], 200);
    }

    public function process(Request $request)
    {
        $cart_ids = explode(",", $request->cart_ids);
        $cart_quantities = explode(",", $request->cart_quantities);

        if (!empty($cart_ids)) {
            $i = 0;
            foreach ($cart_ids as $cart_id) {
                $cart_item = Cart::where('id', $cart_id)->first();
                $product = Product::where('id', $cart_item->product_id)->first();

                if ($product->min_qty > $cart_quantities[$i]) {
                    return response()->json(['result' => false, 'message' => "Minimum {$product->min_qty} item(s) should be ordered for {$product->name}"], 200);
                }
                $cart_item->update([
                    'quantity' => $cart_quantities[$i]
                ]);
                $i++;
            }

            return response()->json(['result' => true, 'message' => 'Cart updated'], 200);
        } else {
            return response()->json(['result' => false, 'message' => 'Cart is empty'], 200);
        }
    }

    public function destroy($id)
    {
        $cart_item = Cart::where('id', $id)->first();
        Cart::destroy($id);
        return response()->json(['result' => true, 'cartQty' => Cart::where('user_id', $cart_item->user_id)->count(), 'message' => 'Product is successfully removed from your cart'], 200);
    }

    public function count($id)
    {
        $items = Cart::where('user_id', $id)->get()->count();
        return response()->json([
            'count' => ($items),
            'status' => true,
        ]);
    }
}
