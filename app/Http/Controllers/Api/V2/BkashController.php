<?php


namespace App\Http\Controllers\Api\V2;

use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Addon;
use App\Models\BusinessSetting;

use Illuminate\Http\Request;
use Session;

class BkashController extends Controller
{

    public function begin(Request $request)
    {

        $payment_type = $request->payment_type;
        $order_id = $request->order_id;
        $amount = $request->amount;
        $user_id = $request->user_id;

        try {
            $request_data = array('app_key' => env('BKASH_CHECKOUT_APP_KEY'), 'app_secret' => env('BKASH_CHECKOUT_APP_SECRET'));

            $url = curl_init('https://checkout.pay.bka.sh/v1.2.0-beta/checkout/token/grant');
            $request_data_json = json_encode($request_data);

            $header = array(
                'Content-Type:application/json',
                'username:' . env('BKASH_CHECKOUT_USER_NAME'),
                'password:' . env('BKASH_CHECKOUT_PASSWORD')
            );
            curl_setopt($url, CURLOPT_HTTPHEADER, $header);
            curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($url, CURLOPT_POSTFIELDS, $request_data_json);
            curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

            $resultdata = curl_exec($url);
            curl_close($url);
            $token = json_decode($resultdata)->id_token;

            return response()->json([
                'token' => $token,
                'result' => true,
                'url' => route('api.bkash.webpage', ["token" => $token, "amount" => $request->amount]),
                'message' => 'Payment page is found'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'token' => '',
                'result' => false,
                'url' => '',
                'message' => $exception->getMessage()
            ]);
        }


    }

    public function webpage($token, $amount)
    {
        return view('mobile_app.bkash', compact('token', 'amount'));
    }

    public function checkout($token,$amount)
    {
        $auth = $token;

        $callbackURL = route('home');

        $requestbody = array(
            'amount' => $amount,
            'currency' => 'BDT',
            'intent' => 'sale'
        );
        $url = curl_init('https://checkout.pay.bka.sh/v1.2.0-beta/checkout/payment/create');
        $requestbodyJson = json_encode($requestbody);

        $header = array(
            'Content-Type:application/json',
            'Authorization:' . $auth,
            'X-APP-Key:' . env('BKASH_CHECKOUT_APP_KEY')
        );

        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $requestbodyJson);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $resultdata = curl_exec($url);
        curl_close($url);

        return $resultdata;
    }

    public function execute($token, Request $request)
    {
        $paymentID = $request->paymentID;
        $auth = $token;

        $url = curl_init('https://checkout.pay.bka.sh/v1.2.0-beta/checkout/payment/execute/' . $paymentID);
        $header = array(
            'Content-Type:application/json',
            'Authorization:' . $auth,
            'X-APP-Key:' . env('BKASH_CHECKOUT_APP_KEY')
        );

        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        $resultdata = curl_exec($url);
        curl_close($url);

        return $resultdata;
    }

    // public function process(Request $request)
    // {
    //     try {

    //         $payment_type = $request->payment_type;

    //         if ($payment_type == 'cart_payment') {

    //             checkout_done($request->order_id, $request->payment_details);
    //         }

    //         if ($payment_type == 'wallet_payment') {

    //             wallet_payment_done($request->user_id, $request->amount, 'Bkash', $request->payment_details);
    //         }

    //         return response()->json(['result' => true, 'message' => "Payment is successful"]);


    //     } catch (\Exception $e) {
    //         return response()->json(['result' => false, 'message' => $e->getMessage()]);
    //     }
    // }

    public function process(Request $request)
    {
        try {

            $payment_type = $request->payment_type;
            $this->checkout_done($request->order_id,$request->payment_details);

            // if ($payment_type == 'cart_payment') {

            //     checkout_done($request->order_id, $request->payment_details);
            // }

            // if ($payment_type == 'wallet_payment') {

            //     wallet_payment_done($request->user_id, $request->amount, 'Bkash', $request->payment_details);
            // }

            return response()->json(['result' => true, 'message' => "Payment is successful"]);

        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => $e->getMessage()]);
        }
    }

    public function success(Request $request)
    {
        return response()->json([
            'result' => true,
            'message' => 'Payment Success',
            'payment_details' => $request->payment_details
        ]);

    }

    public function fail(Request $request)
    {
        return response()->json([
            'result' => false,
            'message' => 'Payment Failed',
            'payment_details' => $request->payment_details
        ]);
    }

    public function checkout_done($order_id, $payment,$amount=0)
    {
        $order = Order::findOrFail($order_id);
		if($amount!=0 && $order->grand_total!=$amount){
			$order->payment_status = 'partial';
		}else
			$order->payment_status = 'paid';
        $order->payment_details = $payment;
        $order->save();

        // if (\App\Models\Addon::where('unique_identifier', 'affiliate_system')->first() != null && \App\Models\Addon::where('unique_identifier', 'affiliate_system')->first()->activated) {
        //     $affiliateController = new AffiliateController;
        //     $affiliateController->processAffiliatePoints($order);
        // }

        // if (\App\Models\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Models\Addon::where('unique_identifier', 'club_point')->first()->activated) {
        //     if (Auth::check()) {
        //         $clubpointController = new ClubPointController;
        //         $clubpointController->processClubPoints($order);
        //     }
        // }

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
        Session::forget('order_id_bkash');
        Session::forget('payment_type');
        Session::forget('payment_from');
    }

}

