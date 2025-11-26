<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\Address;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Product;
use App\Models\OrderDetail;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\User;
use DB;
use App\Models\Referr_code;
use App\Models\Wallet;
use App\Models\Offer;
use App\Models\AffiliateOption;
use App\Models\BusinessSetting;

class OrderController extends Controller
{
    public function store(Request $request, $set_paid = false, $wallet_amt = 0)
    {
        $cartItems = Cart::where('user_id', $request->user_id)->get();

        if ($request->has('order_from')) {
            $order_from = $request->order_from;
        } else {
            $order_from = "IOS";
        }

        if ($cartItems->isEmpty()) {
            return response()->json([
                'order_id' => 0,
                'result' => false,
                'message' => 'Cart is Empty'
            ]);
        }

        $user = User::find($request->user_id);
        $address = Address::where('id', $cartItems->first()->address_id)->first();

        $shippingAddress = [];
        if ($address != null) {
            $shippingAddress['name'] = $user->name;
            $shippingAddress['email'] = $user->email;
            $shippingAddress['address'] = $address->address;
            $shippingAddress['country'] = $address->country;
            $shippingAddress['city'] = $address->city;
            $shippingAddress['postal_code'] = $address->postal_code;
            $shippingAddress['phone'] = $address->phone;
            $shippingAddress['note'] = !empty($request->note) ? $request->note : '';
        }
        // dd($cartItems, $user, $address);
        // dd($shippingAddress);

        $sum = 0.00;
        $spec_discount = 0.00;
        foreach ($cartItems as $cartItem) {
            $item_sum = 0;
            $item_sum += ($cartItem->price + $cartItem->tax) * $cartItem->quantity;
            $item_sum += $cartItem->shipping_cost;
            $sum += $item_sum;   //// 'grand_total' => $request->g
            $spec_discount += $cartItem->special_discount;
        }

        $shipping_skip_total = BusinessSetting::where('type', 'flat_rate_shipping_cost_total')->first()->value;
        if ($shipping_skip_total <= ($sum + $cartItems[0]->coupon_amount)) {
            $calculate_shipping = 0;
        } else {
            $calculate_shipping = BusinessSetting::where('type', 'flat_rate_shipping_cost')->first()->value;
        }
        $sum += $calculate_shipping;

        $exists = Order::whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))->orderBy('created_at', 'desc')->get()->take(1);
        if (count($exists) > 0) {
            $code = date('dmy') . substr($exists[0]->code, -4);
            $code = ((int)$code) + 1;
            // echo $code;
            // dd($exists);
        } else {
            $code = date('dmy') . '0001';
        }

        if ($request->payment_type == 'bkash') {
            $bkashOffer = Offer::where('title', 'Bkash Offer')->where('status', 1)->get();
            if (count($bkashOffer) > 0) {
                $dis = $bkashOffer[0]->discount;
                $type = $bkashOffer[0]->discount_type;
                if ($type == 'percent') {
                    $dis = ($sum * $dis) / 100;
                }
                $cartItems[0]->special_discount = $cartItems[0]->special_discount + $dis;
            }
        }

        // dd('prince');
        // create an order
        $order = Order::create([
            'user_id' => $request->user_id,
            'shipping_address' => json_encode($shippingAddress),
            'payment_type' => $request->payment_type,
            'payment_status' => $set_paid ? 'paid' : 'unpaid',
            'grand_total' => $sum - ($cartItems[0]->special_discount + $cartItems[0]->coupon_amount),
            'coupon_discount' => $cartItems[0]->coupon_amount,
            'special_discount' => $spec_discount,
            'code' => $code,
            'order_from' => $order_from,
            'date' => strtotime('now')
        ]);
        if ($request->payment_type == 'wallet')
            $this->payment_done($order, $wallet_amt);
        foreach ($cartItems as $keyy => $cartItem) {
            $product = Product::find($cartItem->product_id);

            if ($keyy == 0) {
                $shipping_cost = $calculate_shipping;
            } else {
                $shipping_cost = 0;
            }
            // save order details
            OrderDetail::create([
                'order_id' => $order->id,
                'seller_id' => $product->user_id,
                'product_id' => $product->id,
                'variation' => $cartItem->variation,
                'price' => $cartItem->price * $cartItem->quantity,
                'discount' => $cartItem->discount * $cartItem->quantity,
                'tax' => $cartItem->tax * $cartItem->quantity,
                'shipping_cost' => $shipping_cost,
                'quantity' => $cartItem->quantity,
                'payment_status' => $set_paid ? 'paid' : 'unpaid'
            ]);
            $product->update([
                'num_of_sale' => DB::raw('num_of_sale + ' . $cartItem->quantity)
            ]);
        }
        // apply coupon usage

        if ($cartItems->first()->coupon_code != '') {
            CouponUsage::create([
                'user_id' => $request->user_id,
                'order_id' => $order->id,
                'coupon_id' => Coupon::where('code', $cartItems->first()->coupon_code)->first()->id
            ]);
        }

        $user = User::findOrFail($request->user_id);
        $user->carts()->delete();
        $order = Order::findOrFail($order->id);
        try {
            $array['view'] = 'emails.invoice';
            $array['subject'] = translate('Your order has been placed') . ' - ' . $code;
            $array['from'] = 'sales@bazarnao.com';
            $array['order'] = $order;

            //Mail::to('sales@bazarnao.com')->send(new InvoiceEmailManager($array));
            //  Mail::to($user->email)->send(new InvoiceEmailManager($array));
        } catch (\Exception $e) {
        }
        //   }

        return response()->json([
            'order_id' => $order->id,
            'result' => true,
            'message' => translate('Your order has been placed successfully')
        ]);
    }

    function payment_done($order, $wallet_amt)
    {

        $ders = 'Payment from wallet';
        $amount = $wallet_amt;
        $wallet = new Wallet();
        $wallet->user_id = $order->user_id;
        $wallet->order_id = $order->id;
        $wallet->payment_method = 'Payment from wallet for order code - ' . $order->code;
        $wallet->amount = (-1 * abs($amount));
        $wallet->payment_details = json_encode(array('payment_method' => 'Wallet', 'order_id' => $order->id));
        $wallet->save();
        $oVal = (object)[
            'amount' => $amount,
            'status' => 'VALID',
            'error' => null
        ];
        $order->payment_details = json_encode($oVal);
        $order->save();
    }

    function checkForFirstOrder($user_id)
    {
        $status = AffiliateOption::where('type', 'download_app')->first()->status;
        if ($status == 0) {
            return false;
        }

        $order = Order::leftJoin('order_details', 'orders.id', '=', 'order_details.order_id')->where('user_id', $user_id)->whereNotIn('order_details.delivery_status', ['cancel'])->groupBy('orders.id')->count();
        if (!empty($order) && $order == 1) {
            $order = Order::leftJoin('order_details', 'orders.id', '=', 'order_details.order_id')->where('user_id', $user_id)->whereNotIn('order_details.delivery_status', ['cancel'])->groupBy('orders.id')->first();
            if ($order->grand_total >= 1000) {
                $r = Referr_code::where('used_by', $user_id)->first();
                if (!empty($r)) {
                    $amount = AffiliateOption::where('type', 'download_app')->first()->percentage;
                    $us = User::findOrFail($user_id);
                    $wallet = new Wallet();
                    $wallet->user_id = $r->user_id;
                    $wallet->payment_method = 'Referral Rewards for first purchase-' . $us->customer->customer_id;
                    $wallet->amount = $amount;
                    $wallet->payment_details = json_encode(array('code' => $r->referr_code, 'user_id' => $user_id));
                    $wallet->save();
                    $user = User::findOrFail($r->user_id);
                    $user->balance = $user->balance + $amount;
                    $user->save();
                }
            }
        }
    }
}
