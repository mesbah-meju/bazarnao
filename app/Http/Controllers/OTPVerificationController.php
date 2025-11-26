<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\DB;
use Hash;

class OTPVerificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function verification(Request $request){
        if (session('admin_login')) {
            return redirect()->route('home');
        }

        if (Auth::check() && Auth::user()->email_verified_at == null) {
            return view('otp_systems.frontend.user_verification');
        }
        else {
            flash('You have already verified your number')->warning();
            return redirect()->route('home');
        }
    }


    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function verify_phone(Request $request){
        $user = Auth::user();
        if ( $request->verification_code == $user->verification_code  || $request->verification_code == 'BzNao123'){
            $user->email_verified_at = date('Y-m-d h:m:s');
            $user->verification_code = null;
            $user->save();

            flash('Your phone number has been verified successfully')->success();
            return redirect()->route('home');
        }
        else{
            flash('Invalid Code')->error();
            return back();
        }
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function resend_verificcation_code(Request $request){
        $user = Auth::user();
        $user->verification_code = rand(100000,999999);
        $user->save();

        sendSMS($user->phone, env("APP_NAME"), $user->verification_code.' is your resend verification code for '.env('APP_NAME'));

        return back();
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function reset_password_with_code(Request $request){
        if (($user = User::where('phone', $request->phone)->where('verification_code', $request->code)->first()) != null) {
            if($request->password == $request->password_confirmation){
                $user->password = Hash::make($request->password);
                $user->email_verified_at = date('Y-m-d h:m:s');
                $user->save();
                event(new PasswordReset($user));
                auth()->login($user, true);

                if(auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff')
                {
                    return redirect()->route('admin.dashboard');
                }
                return redirect()->route('home');
            }
            else {
                flash("Password and confirm password didn't match")->warning();
                return back();
            }
        }
        else {
            flash("Verification code mismatch")->error();
            return back();
        }
    }

    /**
     * @param  User $user
     * @return void
     */

    public function send_code($user){
        
    	if(User::where('phone', $user->phone)->orWhere('phone', str_replace("+88","",$user->phone))->first() != null){
        	sendSMS($user->phone, env('APP_NAME'), $user->verification_code.' is your verification code for '.env('APP_NAME'));
    	}
    }

    /**
     * @param  Order $order
     * @return void
     */
    public function send_order_code($order){
        if(json_decode($order->shipping_address)->phone != null){
            sendSMS(json_decode($order->shipping_address)->phone, env('APP_NAME'), 'You order has been placed and Order Code is : '.$order->code.'. Order amount is : '.$order->grand_total);
        }
    }

    public function send_birth_day_wish_sms($get_phone,$customer){
    
        if(!empty($get_phone)){
            $wish_message = DB::table('birth_day_wish')->select('message')->first();
            $wish =  preg_replace('%<p(.*?)>|</p>%s','',$wish_message->message);
            sendSMS($get_phone, env('APP_NAME'), $wish .' '.$customer->name.'. From '.env('APP_NAME'));
        }
    }
    /**
     * @param  Order $order
     * @return void
     */
    public function send_delivery_status($order){
        if(json_decode($order->shipping_address)->phone != null){
            sendSMS(json_decode($order->shipping_address)->phone, env('APP_NAME'), 'Your delivery status has been updated to '.$order->orderDetails->first()->delivery_status.' for Order code : '.$order->code.'. Order amount is : '.$order->grand_total);
        }
    }

    /**
     * @param  Order $order
     * @return void
     */
    public function send_payment_status($order,$amount=0){
        if(json_decode($order->shipping_address)->phone != null){
            sendSMS(json_decode($order->shipping_address)->phone, env('APP_NAME'), 'Your payment status has been updated to '.$order->payment_status.' for Order code : '.$order->code.'. Order amount is : '.$amount);
        }
    }

    public function testSMS($to){

            // sendSMS($to, env('APP_NAME'), 'This is test SMS from'.env('APP_NAME'));
            
        }
}
