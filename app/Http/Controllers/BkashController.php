<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerPackage;
use App\Models\SellerPackage;
use App\Models\Order;
use App\Models\BusinessSetting;
use App\Models\Seller;
use DB;
use Session;

class BkashController extends Controller
{
    private $base_url;
    public function __construct()
    {
        if(get_setting('bkash_sandbox', 1)){
            $this->base_url = "https://checkout.sandbox.bka.sh/v1.2.0-beta/";
        }
        else {
            $this->base_url = "https://checkout.pay.bka.sh/v1.2.0-beta/";
        }
    }

    public function pay(){
        $amount = 0;
        $payment_from = Session::get('payment_from'); 
        $ledger_sql="select sum(credit) as total_credit from customer_ledger where type='Payment' and order_id=".Session::get('order_id'); //added by alauddin
        $ledger_info=DB::select($ledger_sql); 


        
        if(Session::has('payment_type')){
            if(Session::get('payment_type') == 'cart_payment'){
                $order = Order::findOrFail(Session::get('order_id'));
                if($payment_from=="shopping_cart"){
                    $amount = round($order->grand_total); 
                }else{   
                    
                    if(!empty($ledger_info)){
                        
                        $amount=round($order->grand_total-$ledger_info[0]->total_credit); 
                    }else{
                        $amount = round($order->grand_total);
                    } 
                }
            }
            elseif (Session::get('payment_type') == 'wallet_payment') {
                $amount = round(Session::get('payment_data')['amount']);
            }
            elseif (Session::get('payment_type') == 'customer_package_payment') {
                $customer_package = CustomerPackage::findOrFail(Session::get('payment_data')['customer_package_id']);
                $amount = round($customer_package->amount);
            }
            elseif (Session::get('payment_type') == 'seller_package_payment') {
                $seller_package = SellerPackage::findOrFail(Session::get('payment_data')['seller_package_id']);
                $amount = round($seller_package->amount);
            }
        }

        $request_data = array('app_key'=> env('BKASH_CHECKOUT_APP_KEY'), 'app_secret'=>env('BKASH_CHECKOUT_APP_SECRET'));

        $url = curl_init($this->base_url.'checkout/token/grant');
        $request_data_json=json_encode($request_data);

        $header = array(
                'Content-Type:application/json',
                'username:'.env('BKASH_CHECKOUT_USER_NAME'),
                'password:'.env('BKASH_CHECKOUT_PASSWORD')
                );
                
        curl_setopt($url,CURLOPT_HTTPHEADER, $header);
        curl_setopt($url,CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url,CURLOPT_POSTFIELDS, $request_data_json);
        curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $resultdata = curl_exec($url);
        curl_close($url);
		
        $token = json_decode($resultdata)->id_token;

        Session::put('bkash_token', $token);
        Session::put('payment_amount', $amount);

        return view('frontend.bkash.index');
    }

    public function checkout(Request $request){
        $auth = Session::get('bkash_token');

        $callbackURL = route('home');

        $order = Order::findOrFail(Session::get('order_id'));
        $requestbody = array(
            'amount' => Session::get('payment_amount'),
            'currency' => 'BDT',
            'intent' => 'sale',
			'merchantInvoiceNumber'=>!empty($order) ? $order->code : Session::get('order_id')
        );
        $url = curl_init($this->base_url.'checkout/payment/create');
        $requestbodyJson = json_encode($requestbody);

        $header = array(
            'Content-Type:application/json',
            'Authorization:' . $auth,
            'X-APP-Key:'.env('BKASH_CHECKOUT_APP_KEY')
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

    public function excecute(Request $request){
        $paymentID = $request->paymentID;
        $auth = Session::get('bkash_token');

        $url = curl_init($this->base_url.'checkout/payment/execute/'.$paymentID);
        $header = array(
            'Content-Type:application/json',
            'Authorization:' . $auth,
            'X-APP-Key:'.env('BKASH_CHECKOUT_APP_KEY')
        );

        curl_setopt($url,CURLOPT_HTTPHEADER, $header);
        curl_setopt($url,CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url,CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($url, CURLOPT_TIMEOUT, 30);

        curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);
        $resultdata = curl_exec($url);
        
    	if(curl_errno($url)==28)
		{
        	curl_close($url);
    		return $this->queryPayment($paymentID);
		}else{
        	curl_close($url);
        	return $resultdata;
        }
    }

    public function queryPayment($paymentID){
        $auth = $this->getToken();

        $url = curl_init($this->base_url.'checkout/payment/query/'.$paymentID);
        $header = array(
            'Content-Type:application/json',
            'Authorization:' . $auth,
            'X-APP-Key:'.env('BKASH_CHECKOUT_APP_KEY')
        );

        curl_setopt($url,CURLOPT_HTTPHEADER, $header);
        curl_setopt($url,CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($url,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);
        $resultdata = curl_exec($url);
        curl_close($url);

        return $resultdata;
    }

    public function query_payment(Request $request){
        $paymentID = $request->paymentID;
        $auth = $this->getToken();

        $url = curl_init($this->base_url.'checkout/payment/query/'.$paymentID);
        $header = array(
            'Content-Type:application/json',
            'Authorization:' . $auth,
            'X-APP-Key:'.env('BKASH_CHECKOUT_APP_KEY')
        );

        curl_setopt($url,CURLOPT_HTTPHEADER, $header);
        curl_setopt($url,CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($url,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);
        $resultdata = curl_exec($url);
        curl_close($url);

        return $resultdata;
    }
	
	public function search_payment(Request $request){
       
        $paymentID = $request->paymentID;
        $auth = $this->getToken();

        $url = curl_init($this->base_url.'checkout/payment/search/'.$paymentID);
        $header = array(
            'Content-Type:application/json',
            'Authorization:' . $auth,
            'X-APP-Key:'.env('BKASH_CHECKOUT_APP_KEY')
        );

        curl_setopt($url,CURLOPT_HTTPHEADER, $header);
        curl_setopt($url,CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($url,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);
        $resultdata = curl_exec($url);
        curl_close($url);
        return $resultdata;
    }
    function getToken(){
	    $request_data = array('app_key'=> env('BKASH_CHECKOUT_APP_KEY'), 'app_secret'=>env('BKASH_CHECKOUT_APP_SECRET'));

        $url = curl_init($this->base_url.'checkout/token/grant');
        $request_data_json=json_encode($request_data);

        $header = array(
                'Content-Type:application/json',
                'username:'.env('BKASH_CHECKOUT_USER_NAME'),
                'password:'.env('BKASH_CHECKOUT_PASSWORD')
                );
        curl_setopt($url,CURLOPT_HTTPHEADER, $header);
        curl_setopt($url,CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url,CURLOPT_POSTFIELDS, $request_data_json);
        curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $resultdata = curl_exec($url);
        curl_close($url);

        $token = json_decode($resultdata)->id_token;

        return $token;
    }

    public function success(Request $request){
        $payment_type = Session::get('payment_type');

        $payment_from = Session::get('payment_from'); 

        if ($payment_type == 'cart_payment') {

            if($payment_from=="shopping_cart"){
                $checkoutController = new CheckoutController;
                return $checkoutController->checkout_done(Session::get('order_id'), $request->payment_details);
            }else{
                $purchaseHistoryController = new PurchaseHistoryController;
                $purchaseHistoryController->checkout_done(Session::get('order_id'), $request->payment_details);
                Session::forget('payment_from'); 
                return redirect()->route('purchase_history.index');
            }
        }

        if ($payment_type == 'wallet_payment') {
            $walletController = new WalletController;
            return $walletController->wallet_payment_done(Session::get('payment_data'), $request->payment_details);
        }

    
       
    }
}
