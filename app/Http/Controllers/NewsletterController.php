<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Subscriber;
use Mail;

use App\Mail\EmailManager;

class NewsletterController extends Controller
{
    public function index(Request $request)
    {
        $users = User::all();
        $subscribers = Subscriber::all();
        return view('backend.marketing.newsletters.index', compact('users', 'subscribers'));
    }

    public function send(Request $request)
    {
        if (env('MAIL_USERNAME') != null) {
            //sends newsletter to selected users
        	if ($request->has('user_emails')) {
                foreach ($request->user_emails as $key => $email) {
                    $array['view'] = 'emails.newsletter';
                    $array['subject'] = $request->subject;
                    $array['from'] = env('MAIL_USERNAME');
                    $array['content'] = $request->content;

                    try {
                        Mail::to($email)->queue(new EmailManager($array));
                    } catch (\Exception $e) {
                        dd($e);
                    }
            	}
            }

            //sends newsletter to subscribers
            if ($request->has('subscriber_emails')) {
                foreach ($request->subscriber_emails as $key => $email) {
                    $array['view'] = 'emails.newsletter';
                    $array['subject'] = $request->subject;
                    $array['from'] = env('MAIL_USERNAME');
                    $array['content'] = $request->content;

                    try {
                        Mail::to($email)->queue(new EmailManager($array));
                    } catch (\Exception $e) {
                        dd($e);
                    }
            	}
            }
        }
        else {
            flash(translate('Please configure SMTP first'))->error();
            return back();
        }

    	flash(translate('Newsletter has been send'))->success();
    	return back();
    }

    public function testEmail(Request $request){
        $array['view'] = 'emails.newsletter';
        $array['subject'] = "SMTP Test";
        $array['from'] = env('MAIL_USERNAME');
        $array['content'] = "This is a test email.";

        try {
            Mail::to($request->email)->queue(new EmailManager($array));
        } catch (\Exception $e) {
            dd($e);
        }

        flash(translate('An email has been sent.'))->success();
        return back();
    }

    public function birthdaywishsms(Request $request)
    {
        $message = DB::table('birth_day_wish')->first();
        return view('backend.marketing.birthdaysms',compact('message'));
    }

    public function birthdaysms_store(Request $request)
    {
        $update = DB::table('birth_day_wish')->update(['message' => $request->content]);
        flash(translate('Wish Message Has Been Updated'))->success();
    	return back();
    }

    function send_birth_day_wish(){

        // $today = date('Y-m-d');
        // $customer_info = Customer::select('user_id','dob')->whereDate('customers.dob',$today)->get();
        // foreach($customer_info as $customer ){
        //     if(!empty(($customer->dob) && ($customer->user->phone))){
        //         try {
        //             $otpController = new OTPVerificationController;
        //             $otpController->send_birth_day_wish_sms($customer->user->phone,$customer);
        //         } catch (\Exception $e) {
        //             echo $e->getMessage();
        //         }
    
        //     }
        // }
        // flash(translate('Wish Message Has Been Send Successfully'))->success();
        // return back();



        $customer_info = User::join('customers','customers.user_id','users.id')
        ->select('users.name','users.phone','customers.dob')
        ->where('user_type','customer')
        ->where('customers.dob', '>','1970-01-01')->get();
            
            foreach($customer_info as $customer ){
                if(!empty(($customer->dob) && ($customer->shipping_address))){
                    $get_phone = json_decode($customer->shipping_address)->phone;
                    $date = date('m-d',strtotime($customer->dob));
                    $today =date('m-d');
                    if($date === $today){
                        try {
                            $otpController = new OTPVerificationController;
                            $otpController->send_birth_day_wish_sms($get_phone,$customer);
                        } catch (\Exception $e) {
                        }
                    }
                }
            }
            flash(translate('Wish Message Has Been Send Successfully'))->success();
            return back();
    }
}
