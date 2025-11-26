<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Customer;
use App\Models\BusinessSetting;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OTPVerificationController;
use App\Models\Addon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Cookie;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data,[
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'phone'=> 'required|max:11|min:11',
            'email' => 'email|max:35',
            'birth' => 'required|date|before:today'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    
    protected function user_reg_create(array $data)
    {
        
         if (\App\Models\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Models\Addon::where('unique_identifier', 'otp_system')->first()->activated) {
                $user = User::create([
                    'name' => $data['name'],
                    'birth' => date('Y-m-d',strtotime($data['birth'])),
                    //'phone' => '+' . $data['country_code'] . $data['phone'],
                    'phone' => $data['phone'],
					'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'verification_code' => rand(100000, 999999)
                ]);

                $exists = Customer::where('area_code', $data['area_code'])->orderBy('created_at', 'desc')->first();
                if ($exists){
                    $increment = (int)$exists->customer_id + 1;
                    $customer_id = $increment;
                    if(strlen((string)$customer_id) < 8){
                        $customer_id ='0'.$customer_id;
                    }
                } else{
                    $customer_id = (string)$data['area_code'].'000001';
                    if(strlen((string)$customer_id) < 8){
                        $customer_id ='0'.$customer_id;
                    }
                }

                $customer = new Customer;
                $customer->created_from = 'Web';
                $customer->user_id = $user->id;
                $customer->area_code = $data['area_code'];
                $customer->customer_id = $customer_id;
                $customer->save();

                $otpController = new OTPVerificationController;
                $otpController->send_code($user);
            } else {
				   if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
					$user = User::create([
						'name' => $data['name'],
						'email' => $data['email'],
						'password' => Hash::make($data['password']),
					]);
					$exists = Customer::where('area_code', $data['area_code'])->orderBy('customer_id', 'desc')->get()->take(1);
					if (count($exists) > 0) {
						$customer_id = $exists[0]->customer_id;
						$customer_id= $customer_id+1;
							if(strlen((string)$customer_id) < 8){
								$customer_id ='0'.$customer_id;
							}
					} else {
						$customer_id = (string)$data['area_code'] . '000001';
					}
					$customer = new Customer;
                    $customer->created_from = 'Web';
					$customer->user_id = $user->id;
					$customer->area_code = $data['area_code'];
					$customer->customer_id = $customer_id;
					$customer->save();
				}
        }

        if (Cookie::has('referral_code')) {
            $referral_code = Cookie::get('referral_code');
            $referred_by_user = User::where('referral_code', $referral_code)->first();
            if ($referred_by_user != null) {
                $user->referred_by = $referred_by_user->id;
                $user->save();
            }
        }

        return $user;
    }

    public function register(Request $request)
    {
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)){
            if (User::where('email', $request->email)->first() != null) {
                flash(translate('Email or Phone already exists.'));
                return back();
            }
        } elseif (User::where('phone', '+' . $request->country_code . $request->phone)->first() != null) {
            flash(translate('Phone already exists.'));
            return back();
        }elseif (User::where('phone', $request->phone)->first() != null){
            flash(translate('Phone already exists.'));
            return back();
        }

        $this->validator($request->all())->validate();

        $user = $this->user_reg_create($request->all());

        $this->guard()->login($user);

        if ($user->email != null){
            if (BusinessSetting::where('type', 'email_verification')->first()->value != 1) {
                $user->email_verified_at = date('Y-m-d H:m:s');
                $user->save();
                flash(translate('Registration successfull.'))->success();
            } else {
                //event(new Registered($user));
                flash(translate('Registration successfull. Please verify your email.'))->success();
            }
        }

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }

    protected function registered(Request $request, $user)
    {
      //  if ($user->email == null) {
            return redirect()->route('verification');
      //  } else {
       //     return redirect()->route('home');
      //  }
    }
}
