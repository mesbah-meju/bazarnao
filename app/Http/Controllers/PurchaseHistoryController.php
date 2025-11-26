<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\BusinessSetting;
use Auth;
use DB;

class PurchaseHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $ledger_sql="select sum(credit) as total_credit from customer_ledger where type='Payment' and order_id=12";
        // $ledger_info=DB::select($ledger_sql);
        // dd($ledger_info);
        $orders = Order::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->paginate(9);
        return view('frontend.user.purchase_history', compact('orders'));
    }

    

    public function purchase_history_details(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->delivery_viewed = 1;
        $order->payment_status_viewed = 1;
        $order->save();
        return view('frontend.user.order_details_customer', compact('order'));
    }


    public function order_payment($id)
    {
        $order = Order::findOrFail($id);
        // $address_info=json_decode($order->shipping_address);
        // dd($address_info->name);
        
        //dd($test);
        $products = Product::where('outofstock', '0')->get();
        return view('frontend.user.order_payment', compact('order', 'products'));
    }




    //check the selected payment gateway and redirect to that controller accordingly
    public function checkout(Request $request)
    {
        // echo $request->order_id;
        // echo $request->payment_option;
        // exit;
        // dd($request->grand_total);  
        $total_credit = DB::table('customer_ledger')
            ->where('type', 'Payment')
            ->where('order_id', $request->order_id)
            ->select(DB::raw('sum(credit) as total_credit'))
            ->first()->total_credit ?? 0;
    
        $total_debit = DB::table('customer_ledger')
            ->where('type', 'Order')
            ->where('order_id', $request->order_id)
            ->select(DB::raw('sum(debit) as total_debit'))
            ->first()->total_debit ?? 0;
        
        $payment_amount = $total_debit - $total_credit;

    
    
        if ($request->payment_option != null) {
            // Save Address

            if (Auth::check()) {
               
                $order_address = Order::findOrFail($request->order_id);
                $address_info=json_decode($order_address->shipping_address);
                $data['name'] = $address_info->name;
                $data['email'] = $address_info->email;
                $data['address'] = $address_info->address;
                $data['country'] = $address_info->country;
                $data['city'] = $address_info->city;
                $data['postal_code'] = $address_info->postal_code;
                $data['phone'] = $address_info->phone;
               // $data['note'] = $request->note;
               // $data['checkout_type'] = $request->checkout_type;
            } else {
                $data['name'] = $request->name;
                $data['email'] = $request->email;
                $data['address'] = $request->address;
                $data['country'] = $request->country;
                $data['city'] = $request->city;
                $data['postal_code'] = $request->postal_code;
                $data['phone'] = $request->phone;
                $data['area'] = $request->area;
               // $data['note'] = $request->note;
               // $data['checkout_type'] = $request->checkout_type;
            }

            $shipping_info = $data;

            $request->session()->put('shipping_info', $shipping_info);

            $request->session()->put('order_id',$request->order_id);

            // if (Session::has('cart') && count(Session::get('cart')) == 0) {
            //     flash(translate('Your Cart was empty'))->warning();
            //     return redirect()->route('home');
            // }

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

            //$orderController = new OrderController;
            //$orderController->store($request);

            $request->session()->put('payment_type', 'cart_payment');
            $request->session()->put('payment_from', 'purchase_history');

            if ($request->session()->get('order_id') != null) {
                $order = Order::findOrFail($request->session()->get('order_id'));
                $customer_id = $order->user_id;


               if ($request->payment_option == 'sslcommerz') {
                    $sslcommerz = new PublicSslCommerzPaymentController;
                    return $sslcommerz->index($request);
                } else if ($request->payment_option == 'nagad') {
                    $nagad = new NagadController;
                    return $nagad->getSession();
                } else if ($request->payment_option == 'bkash') {
                    $bkash = new BkashController;
                    return $bkash->pay();
                }elseif ($request->payment_option == 'cash_on_delivery') {
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
                        return $this->checkout_done($request->session()->get('order_id'),null, $order->grand_total);
                    }else {
						$bal = $user->balance + $credit_limit;
                        $user->balance -= ($user->balance+$credit_limit);
                        $user->save();
                        return $this->checkout_done($request->session()->get('order_id'), null,$bal);
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
    public function checkout_done($order_id, $payment,$amount=0)
    {
		//echo $amount;exit;

        $ledger_sql="select sum(credit) as total_credit from customer_ledger where type='Payment' and order_id=".$order_id;
        $ledger_info=DB::select($ledger_sql);


        $order = Order::findOrFail($order_id);

        // if(!empty($ledger_info)){
        //     $amount=$amount+$ledger_info[0]['total_credit']; 
        // } 


        if(isset($ledger_info[0]->total_credit)){
            $amount=round($order->grand_total-$ledger_info[0]->total_credit);
            $net_amount=$amount+$order->grand_total;
        }else{
            $amount=$order->grand_total;
            $net_amount=$amount;
        } 

        
        
		if($amount!=0 && $order->grand_total!=$net_amount){
			$order->payment_status = 'partial';
		}else
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
            if(isset($payment->card_type)){
                $card_type = json_decode($payment->card_type);
                $ders = 'Payment from sslcommerz by' . $card_type;
                $amount = json_decode($payment->amount);
            }else{
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
					$wallet->payment_method = 'Payment from wallet for order code - '.$order->code;
					$wallet->amount = (-1 * abs($amount));
					$wallet->payment_details = json_encode(array('payment_method'=>'Wallet','order_id'=> $order_id));
					$wallet->save();
					 $oVal = (object)[
						'amount'=>$amount,
						'status'=>'VALID',
						'error'=>null
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
        save_customer_ledger($cust_ledger);

        // if (Session::has('cart')) {
        //     Session::put('cart', Session::get('cart')->where('owner_id', '!=', Session::get('owner_id')));
        // }
        // Session::forget('owner_id');
        // Session::forget('payment_type');
        // Session::forget('delivery_info');
        // Session::forget('coupon_id');
        // Session::forget('coupon_discount');


        

        flash(translate('Payment completed'))->success();
        //return view('frontend.order_confirmed', compact('order'));
        return 1;
    }










    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
