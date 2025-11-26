<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\HappyHour;
use App\Models\Group_product;
use App\Models\Category;
use App\Models\CartDetail;
use Cookie;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        return view('frontend.view_cart', compact('categories'));
    }

    public function showCartModal(Request $request)
    {
        $product = Product::find($request->id);
        return view('frontend.partials.addToCart', compact('product'));
    }

    public function updateNavCart(Request $request)
    {
        return view('frontend.partials.cart');
    }

    public function updateRightCart(Request $request)
    {
        return view('frontend.inc.rightsidebar');
    }

    // public function addToCart(Request $request)
    // {
    //     // dd($request);
    //     $product = Product::find($request->id);
    //     $data = array();
    //     $data['id'] = $product->id;
    //     $data['owner_id'] = $product->user_id;
    //     $str = '';
    //     $tax = 0;
    //     $status = 1;
    //     $msg = '';
    //     if ($product->digital != 1 && $request->quantity < $product->min_qty) {
    //         return array('status' => 0, 'view' => view('frontend.partials.minQtyNotSatisfied', [
    //             'min_qty' => $product->min_qty
    //         ])->render());
    //     }

    //     // dd($product);
    //     //check the color enabled or disabled for the product
    //     if ($request->has('color')) {
    //         $str = $request['color'];
    //     }

    //     if ($product->digital != 1) {
    //         if (isset(Product::find($request->id)->choice_options)) {
    //             foreach (json_decode(Product::find($request->id)->choice_options) as $key => $choice) {
    //                 if ($str != null) {
    //                     $str .= '-' . str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
    //                 } else {
    //                     $str .= str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
    //                 }
    //             }
    //         }
    //     }

    //     $data['variant'] = $str;
    //     $price = $product->unit_price;
    //     $flash_deals = \App\Models\FlashDeal::where('status', 1)->get();
    //     $inFlashDeal = false;
    //     $todaytime = strtotime(date('H:i:s'));

    //     foreach ($flash_deals as $flash_deal) {
    //         $flashstart = strtotime(date('H:i:s', $flash_deal->start_date));
    //         $flashend = strtotime(date('H:i:s', $flash_deal->end_date));
    //         if ($flashstart <= $todaytime && $flashend >= $todaytime) {
    //             if ($flash_deal != null && $flash_deal->status == 1  && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && \App\Models\FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', '==', $product->id)->first() != null) {
    //                 $flash_deal_product = \App\Models\FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first();
    //                 $price -= ($price * $flash_deal->discount_percent) / 100;
    //                 $inFlashDeal = true;
    //                 break;
    //             }
    //         }
    //     }

    //     if (!$inFlashDeal) {
    //         $group_product_price = 0;
    //         if ($product->is_group_product == 1) {
    //             $group_product = \App\Models\Group_product::where('group_product_id', $product->id)->get();
    //             foreach ($group_product as $item) {
    //                 $group_product_price += $item->price;
    //             }
    //             $price = $group_product_price;
    //         } else {
    //             if ($product->discount_type == 'percent') {
    //                 $price -= ($price * $product->discount) / 100;
    //             } elseif ($product->discount_type == 'amount') {
    //                 $price -= $product->discount;
    //             }
    //         }
    //     }

    //     if ($product->tax_type == 'percent') {
    //         $tax = ($price * $product->tax) / 100;
    //     } elseif ($product->tax_type == 'amount') {
    //         $tax = $product->tax;
    //     }

    //     $data['quantity'] = $request['quantity'];
    //     $data['price'] = $price;
    //     $data['tax'] = $tax;
    //     $data['shipping'] = 0;
    //     $data['product_referral_code'] = null;


    //     if ($request['quantity'] == null) {
    //         $data['quantity'] = 1;
    //     }

    //     if (Cookie::has('referred_product_id') && Cookie::get('referred_product_id') == $product->id) {
    //         $data['product_referral_code'] = Cookie::get('product_referral_code');
    //     }
    //     $c_data = $data;
    //     if (Auth::check())
    //         $c_data['user_id']  = Auth::user()->id;
    //     else
    //         $c_data['user_id']  = 0;
    //     $c_data['status']  = 'Added';
    //     $c_data['ip']  = $request->ip;
    //     $c_data['digital']  = 0;
    //     CartDetail::insert($c_data);
    //     if ($request->session()->has('cart')) {
    //         $foundInCart = false;
    //         $cart = collect();

    //         foreach ($request->session()->get('cart') as $key => $cartItem) {
    //             if ($cartItem['id'] == $request->id) {
    //                 $foundInCart = true;
    //                 $checkQuantity = $cartItem['quantity'] + $request['quantity'];
    //                 if (($product->max_qty) < $checkQuantity) {
    //                     $msg = 'You Can not add more than ' . ($product->max_qty) . ' Quantity for this product';
    //                     $status = 0;
    //                     session()->flash('flash_message', [
    //                         'status' => 'danger',
    //                         'msg' => $msg
    //                     ]);
    //                     // return response()->json(['status' => $status, 'msg' => $msg]);
    //                 } else {
    //                     $cartItem['quantity'] += $request['quantity'];
    //                     $msg = translate('Item added to your cart!');
    //                     session()->flash('flash_message', [
    //                         'status' => 'success',
    //                         'msg' => $msg
    //                     ]);
    //                 }
    //             }
    //             $cartItem['digital'] = 0;
    //             $cart->push($cartItem);
    //         }

    //         if (!$foundInCart) {
    //             $cart->push($data);
    //             $msg = translate('Item added to your cart!');
    //             session()->flash('flash_message', [
    //                 'status' => 'success',
    //                 'msg' => $msg
    //             ]);
    //         }

    //         $request->session()->put('cart', $cart);
    //     } else {
    //         $cart = collect([$data]);
    //         $request->session()->put('cart', $cart);
    //     }

    //     return array('status' => $status, 'msg' => $msg, 'view' => view('frontend.partials.addedToCart', compact('product', 'data'))->render());
    // }

    // public function addToCart(Request $request)
    // {
    //     $product = Product::find($request->id);
    //     $data = [];
    //     $data['id'] = $product->id;
    //     $data['owner_id'] = $product->user_id;
    //     $str = '';
    //     $tax = 0;
    //     $status = 1;
    //     $msg = '';

    //     if ($product->digital != 1 && $request->quantity < $product->min_qty) {
    //         return [
    //             'status' => 0,
    //             'view' => view('frontend.partials.minQtyNotSatisfied', [
    //                 'min_qty' => $product->min_qty,
    //             ])->render(),
    //         ];
    //     }

    //     if ($request->has('color')) {
    //         $str = $request['color'];
    //     }

    //     if ($product->digital != 1) {
    //         if (isset(Product::find($request->id)->choice_options)) {
    //             foreach (json_decode(Product::find($request->id)->choice_options) as $choice) {
    //                 if ($str != null) {
    //                     $str .= '-' . str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
    //                 } else {
    //                     $str .= str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
    //                 }
    //             }
    //         }
    //     }

    //     $data['variant'] = $str;
    //     $price = $product->unit_price;
    //     $inHappyHour = false;

    //     // Check for Active Happy Hour Deals
    //     $happy_hour = HappyHour::with('happy_hour_products')
    //         ->where('status', 1)
    //         ->where('end_date', '>=', now())
    //         ->first();
    //     $happy_hour_product = null;

    //     if ($happy_hour) {
    //         $happy_hour_product = $happy_hour->happy_hour_products
    //             ->where('product_id', $product->id)
    //             ->first();
    //         if ($happy_hour_product) {
    //             $price -= ($price * $happy_hour_product->discount) / 100;
    //             $inHappyHour = true;
    //         }
    //     }

    //     // Check for Flash Deals
    //     $flash_deals = \App\Models\FlashDeal::where('status', 1)->get();
    //     $inFlashDeal = false;
    //     $todaytime = strtotime(date('H:i:s'));

    //     foreach ($flash_deals as $flash_deal) {
    //         $flashstart = strtotime(date('H:i:s', $flash_deal->start_date));
    //         $flashend = strtotime(date('H:i:s', $flash_deal->end_date));
    //         if ($flashstart <= $todaytime && $flashend >= $todaytime) {
    //             if (
    //                 $flash_deal->status == 1 &&
    //                 strtotime(date('d-m-Y')) >= $flash_deal->start_date &&
    //                 strtotime(date('d-m-Y')) <= $flash_deal->end_date &&
    //                 \App\Models\FlashDealProduct::where('flash_deal_id', $flash_deal->id)
    //                     ->where('product_id', $product->id)
    //                     ->first() != null
    //             ) {
    //                 $flash_deal_product = \App\Models\FlashDealProduct::where('flash_deal_id', $flash_deal->id)
    //                     ->where('product_id', $product->id)
    //                     ->first();
    //                 $price -= ($price * $flash_deal_product->discount_percent) / 100;
    //                 $inFlashDeal = true;
    //                 break;
    //             }
    //         }
    //     }
    //     if (!$inFlashDeal && !$inHappyHour) {
    //         if ($product->is_group_product == 1) {
    //             $group_product_price = 0;
    //             $group_product = \App\Models\Group_product::where('group_product_id', $product->id)->get();
    //             foreach ($group_product as $item) {
    //                 $group_product_price += $item->price;
    //             }
    //             $price = $group_product_price;
    //         } else {
    //             if ($product->discount_type == 'percent') {
    //                 $price -= ($price * $product->discount) / 100;
    //             } elseif ($product->discount_type == 'amount') {
    //                 $price -= $product->discount;
    //             }
    //         }
    //     }

    //     if ($product->tax_type == 'percent') {
    //         $tax = ($price * $product->tax) / 100;
    //     } elseif ($product->tax_type == 'amount') {
    //         $tax = $product->tax;
    //     }

    //     $data['quantity'] = $request['quantity'];
    //     $data['price'] = $price;
    //     $data['tax'] = $tax;
    //     $data['shipping'] = 0;
    //     $data['product_referral_code'] = null;

    //     if ($request['quantity'] == null) {
    //         $data['quantity'] = 1;
    //     }

    //     if (Cookie::has('referred_product_id') && Cookie::get('referred_product_id') == $product->id) {
    //         $data['product_referral_code'] = Cookie::get('product_referral_code');
    //     }

    //     $c_data = $data;
    //     if (Auth::check()) {
    //         $c_data['user_id'] = Auth::user()->id;
    //     } else {
    //         $c_data['user_id'] = 0;
    //     }
    //     $c_data['status'] = 'Added';
    //     $c_data['ip'] = $request->ip;
    //     $c_data['digital'] = 0;

    //     CartDetail::insert($c_data);

    //     if ($request->session()->has('cart')) {
    //         $foundInCart = false;
    //         $cart = collect();

    //         foreach ($request->session()->get('cart') as $cartItem) {
    //             if ($cartItem['id'] == $request->id) {
    //                 $foundInCart = true;
    //                 $checkQuantity = $cartItem['quantity'] + $request['quantity'];
    //                 if ($product->max_qty < $checkQuantity) {
    //                     $msg = 'You cannot add more than ' . ($product->max_qty) . ' Quantity for this product';
    //                     $status = 0;
    //                     session()->flash('flash_message', [
    //                         'status' => 'danger',
    //                         'msg' => $msg,
    //                     ]);
    //                 } else {
    //                     $cartItem['quantity'] += $request['quantity'];
    //                     $msg = translate('Item added to your cart!');
    //                     session()->flash('flash_message', [
    //                         'status' => 'success',
    //                         'msg' => $msg,
    //                     ]);
    //                 }
    //             }
    //             $cartItem['digital'] = 0;
    //             $cart->push($cartItem);
    //         }

    //         if (!$foundInCart) {
    //             $cart->push($data);
    //             $msg = translate('Item added to your cart!');
    //             session()->flash('flash_message', [
    //                 'status' => 'success',
    //                 'msg' => $msg,
    //             ]);
    //         }

    //         $request->session()->put('cart', $cart);
    //     } else {
    //         $cart = collect([$data]);
    //         $request->session()->put('cart', $cart);
    //     }

    //     return [
    //         'status' => $status,
    //         'msg' => $msg,
    //         'view' => view('frontend.partials.addedToCart', compact('product', 'data'))->render(),
    //     ];
    // }

    public function addToCart(Request $request)
    {
        $product = Product::find($request->id);
        $data = [];
        $data['id'] = $product->id;
        $data['owner_id'] = $product->user_id;
        $str = '';
        $tax = 0;
        $status = 1;
        $msg = '';

        if ($product->digital != 1 && $request->quantity < $product->min_qty) {
            return [
                'status' => 0,
                'view' => view('frontend.partials.minQtyNotSatisfied', [
                    'min_qty' => $product->min_qty,
                ])->render(),
            ];
        }

        if ($request->has('color')) {
            $str = $request['color'];
        }

        if ($product->digital != 1) {
            if (isset(Product::find($request->id)->choice_options)) {
                foreach (json_decode(Product::find($request->id)->choice_options) as $choice) {
                    if ($str != null) {
                        $str .= '-' . str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                    } else {
                        $str .= str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                    }
                }
            }
        }

        $data['variant'] = $str;
        $price = $product->unit_price;
        $inHappyHour = false;

        // Check for Active Happy Hour Deals
        $happy_hour = HappyHour::with('happy_hour_products')
            ->where('status', 1)
            ->where('end_date', '>=', now())
            ->first();
        $happy_hour_product = null;

        if ($happy_hour) {
            $happy_hour_product = $happy_hour->happy_hour_products
                ->where('product_id', $product->id)
                ->first();
            if ($happy_hour_product) {
                if($happy_hour_product->discount_type =="percent"){
                    $price -= ($price * $happy_hour_product->discount) / 100;
                }else{
                    $price -= $happy_hour_product->discount;
                }
                $inHappyHour = true;

                // Get the total quantity of the product already in the cart
                $currentQuantityInCart = 0;
                if ($request->session()->has('cart')) {
                    $cart = $request->session()->get('cart');
                    foreach ($cart as $cartItem) {
                        if ($cartItem['id'] == $product->id) {
                            $currentQuantityInCart += $cartItem['quantity'];
                        }
                    }
                }

                // Check if the total quantity exceeds the available web_quantity
                $totalQuantityInCart = $currentQuantityInCart + $request->quantity;
                if ($totalQuantityInCart > $happy_hour_product->web_quantity) {
                    $msg = 'You cannot add more than ' . $happy_hour_product->web_quantity . ' items for this product during Happy Hour.';
                    $status = 0;
                    session()->flash('flash_message', [
                        'status' => 'danger',
                        'msg' => $msg,
                    ]);
                    return [
                        'status' => $status,
                        'msg' => $msg,
                        'view' => view('frontend.partials.addedToCart', compact('product', 'data'))->render(),
                    ];
                }
            }
        }

        // Check for Flash Deals
        $flash_deals = \App\Models\FlashDeal::where('status', 1)->get();
        $inFlashDeal = false;
        $todaytime = strtotime(date('H:i:s'));

        foreach ($flash_deals as $flash_deal) {
            $flashstart = strtotime(date('H:i:s', $flash_deal->start_date));
            $flashend = strtotime(date('H:i:s', $flash_deal->end_date));
            if ($flashstart <= $todaytime && $flashend >= $todaytime) {
                if (
                    $flash_deal->status == 1 &&
                    strtotime(date('d-m-Y')) >= $flash_deal->start_date &&
                    strtotime(date('d-m-Y')) <= $flash_deal->end_date &&
                    \App\Models\FlashDealProduct::where('flash_deal_id', $flash_deal->id)
                    ->where('product_id', $product->id)
                    ->first() != null
                ) {
                    $flash_deal_product = \App\Models\FlashDealProduct::where('flash_deal_id', $flash_deal->id)
                        ->where('product_id', $product->id)
                        ->first();
                    $price -= ($price * $flash_deal_product->discount_percent) / 100;
                    $inFlashDeal = true;
                    break;
                }
            }
        }

        if (!$inFlashDeal && !$inHappyHour) {
            if ($product->is_group_product == 1) {
                $group_product_price = 0;
                $group_product = \App\Models\Group_product::where('group_product_id', $product->id)->get();
                foreach ($group_product as $item) {
                    $group_product_price += $item->price;
                }
                $price = $group_product_price;
            } else {
                if ($product->discount_type == 'percent') {
                    $price -= ($price * $product->discount) / 100;
                } elseif ($product->discount_type == 'amount') {
                    $price -= $product->discount;
                }
            }
        }

        if ($product->tax_type == 'percent') {
            $tax = ($price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $tax = $product->tax;
        }

        $data['quantity'] = $request['quantity'];
        $data['price'] = $price;
        $data['tax'] = $tax;
        $data['shipping'] = 0;
        $data['product_referral_code'] = null;

        if ($request['quantity'] == null) {
            $data['quantity'] = 1;
        }

        if (Cookie::has('referred_product_id') && Cookie::get('referred_product_id') == $product->id) {
            $data['product_referral_code'] = Cookie::get('product_referral_code');
        }

        $c_data = $data;
        if (Auth::check()) {
            $c_data['user_id'] = Auth::user()->id;
        } else {
            $c_data['user_id'] = 0;
        }
        $c_data['status'] = 'Added';
        $c_data['ip'] = $request->ip;
        $c_data['digital'] = 0;

        CartDetail::insert($c_data);

        if ($request->session()->has('cart')) {
            $foundInCart = false;
            $cart = collect();

            foreach ($request->session()->get('cart') as $cartItem) {
                if ($cartItem['id'] == $request->id) {
                    $foundInCart = true;
                    $checkQuantity = $cartItem['quantity'] + $request['quantity'];
                    if ($product->max_qty < $checkQuantity) {
                        $msg = 'You cannot add more than ' . ($product->max_qty) . ' Quantity for this product';
                        $status = 0;
                        session()->flash('flash_message', [
                            'status' => 'danger',
                            'msg' => $msg,
                        ]);
                    } else {
                        $cartItem['quantity'] += $request['quantity'];
                        $msg = translate('Item added to your cart!');
                        session()->flash('flash_message', [
                            'status' => 'success',
                            'msg' => $msg,
                        ]);
                    }
                }
                $cartItem['digital'] = 0;
                $cart->push($cartItem);
            }

            if (!$foundInCart) {
                $cart->push($data);
                $msg = translate('Item added to your cart!');
                session()->flash('flash_message', [
                    'status' => 'success',
                    'msg' => $msg,
                ]);
            }

            $request->session()->put('cart', $cart);
        } else {
            $cart = collect([$data]);
            $request->session()->put('cart', $cart);
        }

        return [
            'status' => $status,
            'msg' => $msg,
            'view' => view('frontend.partials.addedToCart', compact('product', 'data'))->render(),
        ];
    }


    //removes from Cart
    public function removeFromCart(Request $request)
    {
        if ($request->session()->has('cart')) {
            $cart = $request->session()->get('cart', collect([]));
            $cart->forget($request->key);
            $request->session()->put('cart', $cart);
        }

        return view('frontend.partials.cart_details');
    }

    //updated the quantity for a cart item
    // public function updateQuantity(Request $request)
    // {
    //     $cart = $request->session()->get('cart', collect([]));
    //     $msg = 0;
    //     $cart = $cart->map(function ($object, $key) use ($request, $msg) {
    //         if ($key == $request->key) {
    //             $product = \App\Models\Product::find($object['id']);
    //             if (($product->max_qty + 1) <= $request->quantity) {
    //                 $msg = 'You Can not add more than ' . ($product->max_qty) . ' Quantity for this product';
    //                 echo $msg;
    //                 exit;
    //             }
    //             if ($object['variant'] != null && $product->variant_product) {
    //                 $product_stock = $product->stocks->where('variant', $object['variant'])->first();
    //                 $quantity = $product_stock->qty;
    //                 if ($quantity >= $request->quantity) {
    //                     if ($product->outofstock == 0) {
    //                         $object['quantity'] = $request->quantity;
    //                     } else {
    //                         if ($object['variant'] != null && $product->variant_product) {
    //                             $product_stock = $product->stocks->where('variant', $object['variant'])->first();
    //                             $quantity = $product_stock->qty;
    //                             if ($quantity >= $request->quantity) {
    //                                 if ($request->quantity >= $product->min_qty) {
    //                                     $object['quantity'] = $request->quantity;
    //                                 }
    //                             }
    //                         } elseif ($product->current_stock >= $request->quantity) {
    //                             if ($request->quantity >= $product->min_qty) {
    //                                 $object['quantity'] = $request->quantity;
    //                             }
    //                         }
    //                     }
    //                 }
    //             } elseif ($product->outofstock == 0) {
    //                 $object['quantity'] = $request->quantity;
    //             } elseif ($product->current_stock >= $request->quantity) {
    //                 if ($request->quantity >= $product->min_qty) {
    //                     $object['quantity'] = $request->quantity;
    //                 }
    //             }
    //         }
    //         return $object;
    //     });

    //     $request->session()->put('cart', $cart);
    //     echo $msg;
    //     // return view('frontend.partials.cart_details');
    // }

    public function updateQuantity(Request $request)
    {
        $cart = $request->session()->get('cart', collect([]));
        $msg = 0;
        $cart = $cart->map(function ($object, $key) use ($request, $msg) {
            if ($key == $request->key) {
                $product = \App\Models\Product::find($object['id']);
                $happy_hour_product = null;
                $inHappyHour = false;

                // Check for Active Happy Hour Deals and if product is part of the promotion
                $happy_hour = \App\Models\HappyHour::with('happy_hour_products')
                    ->where('status', 1)
                    ->where('end_date', '>=', now())
                    ->first();

                if ($happy_hour) {
                    $happy_hour_product = $happy_hour->happy_hour_products
                        ->where('product_id', $product->id)
                        ->first();
                    if ($happy_hour_product) {
                        $inHappyHour = true;
                    }
                }

                if ($inHappyHour) {
                    // Get the total quantity of the product already in the cart
                    $currentQuantityInCart = 0;
                    if ($request->session()->has('cart')) {
                        $cart = $request->session()->get('cart');
                        foreach ($cart as $cartItem) {
                            if ($cartItem['id'] == $product->id) {
                                $currentQuantityInCart += $cartItem['quantity'];
                            }
                        }
                    }

                    // Check if the total quantity exceeds the available web_quantity
                    $totalQuantityInCart = $request->quantity;
                    if ($totalQuantityInCart > $happy_hour_product->web_quantity) {
                        $msg = 'You cannot add more than ' . $happy_hour_product->web_quantity . ' items for this product during Happy Hour.';
                        echo $msg;
                        exit;
                    }
                }

                // Check max quantity
                if (($product->max_qty + 1) <= $request->quantity) {
                    $msg = 'You cannot add more than ' . ($product->max_qty) . ' Quantity for this product';
                    echo $msg;
                    exit;
                }

                // Check variant and stock conditions
                if ($object['variant'] != null && $product->variant_product) {
                    $product_stock = $product->stocks->where('variant', $object['variant'])->first();
                    $quantity = $product_stock->qty;
                    if ($quantity >= $request->quantity) {
                        if ($product->outofstock == 0) {
                            $object['quantity'] = $request->quantity;
                        } else {
                            if ($request->quantity >= $product->min_qty) {
                                $object['quantity'] = $request->quantity;
                            }
                        }
                    }
                } elseif ($product->outofstock == 0) {
                    $object['quantity'] = $request->quantity;
                } elseif ($product->current_stock >= $request->quantity) {
                    if ($request->quantity >= $product->min_qty) {
                        $object['quantity'] = $request->quantity;
                    }
                }
            }
            return $object;
        });

        $request->session()->put('cart', $cart);
        echo $msg;
    }
}
