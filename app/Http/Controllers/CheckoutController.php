<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Category;
use App\Http\Controllers\InstamojoController;
use App\Http\Controllers\ClubPointController;
use App\Http\Controllers\PublicSslCommerzPaymentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AffiliateController;
use App\Models\AccCoa;
use App\Models\AccPredefineAccount;
use App\Models\Order;
use App\Models\BusinessSetting;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\User;
use App\Models\Address;
use App\Models\FinancialYear;
use Session;
use App\Utility\PayhereUtility;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{

    public function __construct()
    {
        //
    }

    //check the selected payment gateway and redirect to that controller accordingly
    public function checkout(Request $request)
    {
        if ($request->payment_option != null) {
            // Save Address
            if (Auth::check()) {
                if ($request->address_id == null) {
                    flash(translate("Please add shipping address"))->warning();
                    return back();
                }
                $address = Address::findOrFail($request->address_id);
                $data['name'] = Auth::user()->name;
                $data['email'] = Auth::user()->email;
                $data['address'] = $address->address;
                $data['country'] = $address->country;
                $data['city'] = $address->city;
                $data['postal_code'] = $address->postal_code;
                $data['phone'] = $address->phone;
                $data['note'] = $request->note;
                $data['checkout_type'] = $request->checkout_type;
            } else {
                $data['name'] = $request->name;
                $data['email'] = $request->email;
                $data['address'] = $request->address;
                $data['country'] = $request->country;
                $data['city'] = $request->city;
                $data['postal_code'] = $request->postal_code;
                $data['phone'] = $request->phone;
                $data['area'] = $request->area;
                $data['note'] = $request->note;
                $data['checkout_type'] = $request->checkout_type;
            }

            $shipping_info = $data;
            $request->session()->put('shipping_info', $shipping_info);
            if (Session::has('cart') && count(Session::get('cart')) == 0) {
                flash(translate('Your Cart was empty'))->warning();
                return redirect()->route('home');
            }
            // End Address Save

            //$cart = $request->session()->get('cart', collect([]));
            //if (Session::has('cart') && count(Session::get('cart')) > 0) {
            //	flash(translate('Your Cart was empty'))->warning();
            //	return redirect()->route('home');
            // }
            // $cart = $cart->map(function ($object, $key) use ($request) {
            //         $object['shipping_type'] = 'home_delivery';
            //     return $object;
            // });

            // $request->session()->put('cart', $cart);

            // Shipping Method
            $orderController = new OrderController;
            $orderController->store($request);

            $request->session()->put('payment_type', 'cart_payment');

            $request->session()->put('payment_from', 'shopping_cart');

            if ($request->session()->get('order_id') != null) {
                $order = Order::findOrFail($request->session()->get('order_id'));
                if (!empty($request->session()->get('coupon_discount'))) {
                    if (!empty($order->user_id)) {
                        $customer_id = $order->user_id;
                    } else {
                        $customer_id = $order->guest_id;
                    }
                    $cust_ledger = array();
                    $cust_ledger['customer_id'] = $customer_id;
                    $cust_ledger['order_id'] = $order->id;
                    $cust_ledger['descriptions'] = 'Coupon Discount';
                    $cust_ledger['type'] = 'Coupon Discount';
                    $cust_ledger['debit'] = 0;
                    $cust_ledger['credit'] = $request->session()->get('coupon_discount');
                    $cust_ledger['date'] = date('Y-m-d');
                    // save_customer_ledger($cust_ledger);
                }

                if ($request->payment_option == 'sslcommerz') {
                    $sslcommerz = new PublicSslCommerzPaymentController;
                    return $sslcommerz->index($request);
                } elseif ($request->payment_option == 'instamojo') {
                    $instamojo = new InstamojoController;
                    return $instamojo->pay($request);
                } elseif ($request->payment_option == 'razorpay') {
                    $razorpay = new RazorpayController;
                    return $razorpay->payWithRazorpay($request);
                } elseif ($request->payment_option == 'voguepay') {
                    $voguePay = new VoguePayController;
                    return $voguePay->customer_showForm();
                } elseif ($request->payment_option == 'payhere') {
                    $order = Order::findOrFail($request->session()->get('order_id'));

                    $order_id = $order->id;
                    $amount = $order->grand_total;
                    $first_name = json_decode($order->shipping_address)->name;
                    $last_name = 'X';
                    $phone = json_decode($order->shipping_address)->phone;
                    $email = json_decode($order->shipping_address)->email;
                    $address = json_decode($order->shipping_address)->address;
                    $city = json_decode($order->shipping_address)->city;

                    return PayhereUtility::create_checkout_form($order_id, $amount, $first_name, $last_name, $phone, $email, $address, $city);
                } else if ($request->payment_option == 'nagad') {
                    $nagad = new NagadController;
                    return $nagad->getSession();
                } else if ($request->payment_option == 'bkash') {
                    $bkash = new BkashController;
                    return $bkash->pay();
                } elseif ($request->payment_option == 'cash_on_delivery') {
                    $request->session()->put('cart', Session::get('cart')->where('owner_id', '!=', Session::get('owner_id')));
                    $request->session()->forget('owner_id');
                    $request->session()->forget('delivery_info');
                    $request->session()->forget('coupon_id');
                    $request->session()->forget('coupon_discount');
                    $request->session()->forget('offer_discount');

                    flash(translate("Your order has been placed successfully"))->success();
                    return redirect()->route('order_confirmed');
                } elseif ($request->payment_option == 'wallet') {
                    $user = Auth::user();
                    $order = Order::findOrFail($request->session()->get('order_id'));
                    $credit_limit = \App\Models\Customer::where('user_id', Auth::user()->id)->first()->credit_limit;
                    if (($user->balance + $credit_limit) >= $order->grand_total) {
                        $user->balance -= $order->grand_total;
                        $user->save();
                        return $this->checkout_done($request->session()->get('order_id'), null, $order->grand_total);
                    } else {
                        $bal = $user->balance + $credit_limit;
                        $user->balance -= ($user->balance + $credit_limit);
                        $user->save();
                        return $this->checkout_done($request->session()->get('order_id'), null, $bal);
                    }
                } else {
                    $order = Order::findOrFail($request->session()->get('order_id'));
                    $order->manual_payment = 1;
                    $order->save();

                    $request->session()->put('cart', Session::get('cart')->where('owner_id', '!=', Session::get('owner_id')));
                    $request->session()->forget('owner_id');
                    $request->session()->forget('delivery_info');
                    $request->session()->forget('coupon_id');
                    $request->session()->forget('coupon_discount');
                    $request->session()->forget('offer_discount');

                    flash(translate('Your order has been placed successfully. Please submit payment information from purchase history'))->success();
                    return redirect()->route('order_confirmed');
                }
            }
        } else {
            flash(translate('Select Payment Option.'))->warning();
            return back();
        }
    }

    //redirects to this method after a successfull checkout
    public function checkout_done($order_id, $payment, $amount = 0)
    {
        //echo $amount;exit;
        $order = Order::findOrFail($order_id);
        if ($amount != 0 && $order->grand_total != $amount) {
            $order->payment_status = 'partial';
        } else
            $order->payment_status = 'paid';
        $order->payment_details = $payment;
        $order->save();

        if (\App\Models\Addon::where('unique_identifier', 'affiliate_system')->first() != null && \App\Models\Addon::where('unique_identifier', 'affiliate_system')->first()->activated) {
            $affiliateController = new AffiliateController;
            $affiliateController->processAffiliatePoints($order);
        }

        if (\App\Models\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Models\Addon::where('unique_identifier', 'club_point')->first()->activated) {
            if (Auth::check()) {
                $clubpointController = new ClubPointController;
                $clubpointController->processClubPoints($order);
            }
        }
        if (\App\Models\Addon::where('unique_identifier', 'seller_subscription')->first() == null || !\App\Models\Addon::where('unique_identifier', 'seller_subscription')->first()->activated) {
            if (BusinessSetting::where('type', 'category_wise_commission')->first()->value != 1) {
                $commission_percentage = BusinessSetting::where('type', 'vendor_commission')->first()->value;
                foreach ($order->orderDetails as $key => $orderDetail) {
                    $orderDetail->payment_status = 'paid';
                    $orderDetail->save();
                    if ($orderDetail->product->user->user_type == 'seller') {
                        $seller = $orderDetail->product->user->seller;
                        $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price * (100 - $commission_percentage)) / 100 + $orderDetail->tax + $orderDetail->shipping_cost;
                        $seller->save();
                    }
                }
            } else {
                foreach ($order->orderDetails as $key => $orderDetail) {
                    $orderDetail->payment_status = 'paid';
                    $orderDetail->save();
                    if ($orderDetail->product->user->user_type == 'seller') {
                        $commission_percentage = $orderDetail->product->category->commision_rate;
                        $seller = $orderDetail->product->user->seller;
                        $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price * (100 - $commission_percentage)) / 100 + $orderDetail->tax + $orderDetail->shipping_cost;
                        $seller->save();
                    }
                }
            }
        } else {
            foreach ($order->orderDetails as $key => $orderDetail) {
                $orderDetail->payment_status = 'paid';
                $orderDetail->save();
                if ($orderDetail->product->user->user_type == 'seller') {
                    $seller = $orderDetail->product->user->seller;
                    $seller->admin_to_pay = $seller->admin_to_pay + $orderDetail->price + $orderDetail->tax + $orderDetail->shipping_cost;
                    $seller->save();
                }
            }
        }

        $order->commission_calculated = 1;
        $order->save();

        if (!empty($payment)) {
            if (isset($payment->card_type)) {
                $card_type = json_decode($payment->card_type);
                $ders = 'Payment from sslcommerz by' . $card_type;
                $amount = json_decode($payment->amount);
            } else {
                $pmt = json_decode($payment);
                $ders = 'Payment from Bkash';
                $amount = ($pmt->amount);
            }
        } else {
            $ders = 'Payment from wallet';
            $amount = $amount;
            $wallet = new Wallet();
            $wallet->user_id = $order->user_id;
            $wallet->order_id = $order_id;
            $wallet->payment_method = 'Payment from wallet for order code - ' . $order->code;
            $wallet->amount = (-1 * abs($amount));
            $wallet->payment_details = json_encode(array('payment_method' => 'Wallet', 'order_id' => $order_id));
            $wallet->save();
            $oVal = (object)[
                'amount' => $amount,
                'status' => 'VALID',
                'error' => null
            ];
            $order->payment_details = json_encode($oVal);
            $order->save();
        }

        if (!empty($order->user_id)) {
            $customer_id = $order->user_id;
        } else {
            $customer_id = $order->guest_id;
        }
        $cust_ledger = array();
        $cust_ledger['customer_id'] = $customer_id;
        $cust_ledger['order_id'] = $order_id;
        $cust_ledger['descriptions'] = $ders;
        $cust_ledger['type'] = 'Payment';
        $cust_ledger['debit'] = 0;
        $cust_ledger['credit'] = $amount;
        $cust_ledger['date'] = date('Y-m-d');
        // save_customer_ledger($cust_ledger);

        if (Session::has('cart')) {
            Session::put('cart', Session::get('cart')->where('owner_id', '!=', Session::get('owner_id')));
        }
        Session::forget('owner_id');
        Session::forget('payment_type');
        Session::forget('delivery_info');
        Session::forget('coupon_id');
        Session::forget('coupon_discount');


        flash(translate('Payment completed'))->success();
        return view('frontend.order_confirmed', compact('order'));
    }

    public function get_shipping_info(Request $request)
    {
        if (Session::has('cart') && count(Session::get('cart')) > 0) {
            $categories = Category::all();
            return view('frontend.shipping_info', compact('categories'));
        }
        flash(translate('Your cart is empty'))->success();
        return back();
    }

    public function store_shipping_info(Request $request)
    {
        if (Auth::check()) {
            if ($request->address_id == null) {
                flash(translate("Please add shipping address"))->warning();
                return back();
            }
            $address = Address::findOrFail($request->address_id);
            $data['name'] = Auth::user()->name;
            $data['email'] = Auth::user()->email;
            $data['address'] = $address->address;
            $data['country'] = $address->country;
            $data['city'] = $address->city;
            $data['postal_code'] = $address->postal_code;
            $data['phone'] = $address->phone;
            $data['checkout_type'] = $request->checkout_type;
        } else {
            $data['name'] = $request->name;
            $data['email'] = $request->email;
            $data['address'] = $request->address;
            $data['country'] = $request->country;
            $data['city'] = $request->city;
            $data['postal_code'] = $request->postal_code;
            $data['phone'] = $request->phone;
            $data['checkout_type'] = $request->checkout_type;
        }

        $shipping_info = $data;
        $request->session()->put('shipping_info', $shipping_info);

        $subtotal = 0;
        $tax = 0;
        $shipping = 0;
        foreach (Session::get('cart') as $key => $cartItem) {
            $subtotal += $cartItem['price'] * $cartItem['quantity'];
            $tax += $cartItem['tax'] * $cartItem['quantity'];
            $shipping += $cartItem['shipping'] * $cartItem['quantity'];
        }

        $total = $subtotal + $tax + $shipping;

        if (Session::has('coupon_discount')) {
            $total -= Session::get('coupon_discount');
        }

        return view('frontend.delivery_info');
        // return view('frontend.payment_select', compact('total'));
    }

    public function store_delivery_info(Request $request)
    {
        $request->session()->put('owner_id', $request->owner_id);

        if (Session::has('cart') && count(Session::get('cart')) > 0) {
            $cart = $request->session()->get('cart', collect([]));
            $cart = $cart->map(function ($object, $key) use ($request) {
                if (\App\Models\Product::find($object['id'])->user_id == $request->owner_id) {
                    if ($request['shipping_type_' . $request->owner_id] == 'pickup_point') {
                        $object['shipping_type'] = 'pickup_point';
                        $object['pickup_point'] = $request['pickup_point_id_' . $request->owner_id];
                    } else {
                        $object['shipping_type'] = 'home_delivery';
                    }
                }
                return $object;
            });

            $request->session()->put('cart', $cart);

            $cart = $cart->map(function ($object, $key) use ($request) {
                if (\App\Models\Product::find($object['id'])->user_id == $request->owner_id) {
                    if ($object['shipping_type'] == 'home_delivery') {
                        $object['shipping'] = getShippingCost($key);
                    } else {
                        $object['shipping'] = 0;
                    }
                } else {
                    $object['shipping'] = 0;
                }
                return $object;
            });

            $request->session()->put('cart', $cart);

            $subtotal = 0;
            $tax = 0;
            $shipping = 0;
            foreach (Session::get('cart') as $key => $cartItem) {
                $subtotal += $cartItem['price'] * $cartItem['quantity'];
                $tax += $cartItem['tax'] * $cartItem['quantity'];
                $shipping += $cartItem['shipping'] * $cartItem['quantity'];
            }

            $total = $subtotal + $tax + $shipping;

            if (Session::has('coupon_discount')) {
                $total -= Session::get('coupon_discount');
            }


            return view('frontend.payment_select', compact('total'));
        } else {
            flash(translate('Your Cart was empty'))->warning();
            return redirect()->route('home');
        }
    }

    public function get_payment_info(Request $request)
    {
        $request->owner_id = User::where('user_type', 'admin')->first()->id;
        $request->session()->put('owner_id', $request->owner_id);
        if (Session::has('cart') && count(Session::get('cart')) > 0) {
            $cart = $request->session()->get('cart', collect([]));
            $cart = $cart->map(function ($object, $key) use ($request) {
                $object['shipping_type'] = 'home_delivery';

                return $object;
            });

            $request->session()->put('cart', $cart);

            $cart = $cart->map(function ($object, $key) use ($request) {
                if (\App\Models\Product::find($object['id'])->user_id == $request->owner_id) {
                    if ($object['shipping_type'] == 'home_delivery') {
                        $object['shipping'] = getShippingCost($key);
                    } else {
                        $object['shipping'] = 0;
                    }
                } else {
                    $object['shipping'] = 0;
                }
                return $object;
            });

            $request->session()->put('cart', $cart);

            // $subtotal = 0;
            // $tax = 0;
            // $shipping = 0;
            // foreach (Session::get('cart') as $key => $cartItem) {
            //     $subtotal += $cartItem['price'] * $cartItem['quantity'];
            //     $tax += $cartItem['tax'] * $cartItem['quantity'];
            //     $shipping += $cartItem['shipping'] * $cartItem['quantity'];
            // }

            // $total = $subtotal + $tax + $shipping;

            // if (Session::has('coupon_discount')) {
            //     $total -= Session::get('coupon_discount');
            // }

            $subtotal = 0;
            $tax = 0;
            $shipping = 0;
            foreach (Session::get('cart') as $key => $cartItem) {
                if (isset($cartItem['offer']) && $cartItem['offer'] == 1) {
                    $cart->forget($key);
                    continue;
                }
                $subtotal += $cartItem['price'] * $cartItem['quantity'];
                $tax += $cartItem['tax'] * $cartItem['quantity'];
                $shipping += $cartItem['shipping'] * $cartItem['quantity'];
            }
            $shipping_skip_total = \App\Models\BusinessSetting::where('type', 'flat_rate_shipping_cost_total')->first()->value;
            if ($shipping_skip_total > $subtotal) {
                $shipping = \App\Models\BusinessSetting::where('type', 'flat_rate_shipping_cost')->first()->value;
            } else {
                $shipping = 0;
            }

            $total = $subtotal + $tax + $shipping;
            //echo $shipping;exit;
            $total = calculate_discount($total);
            if (Session::has('coupon_discount')) {
                $total -= Session::get('coupon_discount');
            }
            if (Session::has('cart') && count(Session::get('cart')) == 0) {
                flash(translate('Your Cart was empty'))->warning();
                return redirect()->route('home');
            }

            return view('frontend.payment_select', compact('total'));
        } else {
            flash(translate('Your Cart was empty'))->warning();
            return redirect()->route('home');
        }
    }

    public function apply_coupon_code(Request $request)
    {
        //dd($request->all());
        $coupon = Coupon::where('code', $request->code)->first();

        if ($coupon != null) {
            if (strtotime(date('d-m-Y')) >= $coupon->start_date && strtotime(date('d-m-Y')) <= $coupon->end_date) {
                if (CouponUsage::where('user_id', Auth::user()->id)->where('coupon_id', $coupon->id)->count() < $coupon->max_times) {
                    $coupon_details = json_decode($coupon->details);

                    if ($coupon->type == 'cart_base') {
                        $subtotal = 0;
                        $tax = 0;
                        $shipping = 0;
                        foreach (Session::get('cart') as $key => $cartItem) {
                            $subtotal += $cartItem['price'] * $cartItem['quantity'];
                            $tax += $cartItem['tax'] * $cartItem['quantity'];
                            $shipping += $cartItem['shipping'] * $cartItem['quantity'];
                        }
                        $sum = $subtotal + $tax + $shipping;

                        if ($sum >= $coupon_details->min_buy) {
                            if ($coupon->discount_type == 'percent') {
                                $coupon_discount = ($sum * $coupon->discount) / 100;
                                if ($coupon_discount > $coupon_details->max_discount) {
                                    $coupon_discount = $coupon_details->max_discount;
                                }
                            } elseif ($coupon->discount_type == 'amount') {
                                $coupon_discount = $coupon->discount;
                            }
                            $request->session()->put('coupon_id', $coupon->id);
                            $request->session()->put('coupon_discount', $coupon_discount);
                            flash(translate('Coupon has been applied'))->success();
                        }
                    } elseif ($coupon->type == 'product_base') {
                        $coupon_discount = 0;
                        foreach (Session::get('cart') as $key => $cartItem) {
                            foreach ($coupon_details as $key => $coupon_detail) {
                                if ($coupon_detail->product_id == $cartItem['id']) {
                                    if ($coupon->discount_type == 'percent') {
                                        $coupon_discount += $cartItem['price'] * $coupon->discount / 100;
                                    } elseif ($coupon->discount_type == 'amount') {
                                        $coupon_discount += $coupon->discount;
                                    }
                                }
                            }
                        }
                        $request->session()->put('coupon_id', $coupon->id);
                        $request->session()->put('coupon_discount', $coupon_discount);
                        flash(translate('Coupon has been applied'))->success();
                    }
                } else {
                    flash(translate('You already used this coupon maximum times!'))->warning();
                }
            } else {
                flash(translate('Coupon expired!'))->warning();
            }
        } else {
            flash(translate('Invalid coupon!'))->warning();
        }
        return back();
    }

    public function remove_coupon_code(Request $request)
    {
        $request->session()->forget('coupon_id');
        $request->session()->forget('offer_discount');
        $request->session()->forget('coupon_discount');
        return back();
    }

    public function order_confirmed()
    {
        $order = Order::findOrFail(Session::get('order_id'));

        return view('frontend.order_confirmed', compact('order'));
    }
}
