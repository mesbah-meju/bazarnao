<?php /** @noinspection PhpUndefinedClassInspection */

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\OTPVerificationController;
use App\Models\Customer;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use Hash;
use App\Notifications\AppEmailVerificationNotification;
use GeneaLabs\LaravelSocialiter\Facades\Socialiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Sanctum\PersonalAccessToken;
use Socialite;
use App\Models\Cart;
use App\Rules\Recaptcha;
use App\Services\SocialRevoke;

class AuthController extends Controller
{
   
    public function signup(Request $request)
    {
        if($request->register_by){

            $user = User::where('phone', $request->phone)->orWhere('email',$request->email)->first();
            if ($user){
                return response()->json([
                    'result' => false,
                    'message' => 'User already exists',
                    'user_id' => 0
                ], 201);
            }

         
                $user = new User([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'birth' => $request->dob,
                    'password' => bcrypt($request->password),
                    'verification_code' => rand(100000, 999999)
                ]);
                $user->save();
    
                $otpController = new OTPVerificationController();
                $otpController->send_code($user);
                
       
             $exists = Customer::where('area_code', $request->register_by)->orderBy('created_at', 'desc')->first();
              if($exists){
                $increment = $exists->customer_id + 1;
                $customer_id = $increment;
                if(strlen((string)$customer_id) < 8){
                    $customer_id ='0'.$customer_id;
                }
              }else{
                $customer_id = $request->register_by . '000001';
                if(strlen((string)$customer_id) < 8){
                    $customer_id ='0'.$customer_id;
                }
             }
    
            $customer = new Customer;
            $customer->created_from = 'App';
            $customer->user_id = $user->id;
            $customer->dob = $request->dob;
            $customer->area_code = $request->area_code;
            $customer->customer_id = $customer_id;
            $customer->save();

           
                
            $user->createToken('tokens')->plainTextToken;

            return $this->loginSuccess($user);
        }
            // return response()->json([
            //     'result' => true,
            //     'customer' => $customer,
            //     'access_token' => $tokenResult->accessToken,
            //     'token_type' => 'Bearer',
            //     'user' => $user,
            //     'message' => 'Registration Successful. Please verify and log in to your account.',
            //     'user_id' => $user->id
            // ], 201);
            
        //    }else{
        //     return response()->json([
        //         'result' => false,
        //         'message' => 'Please Select Area',
        //         'user_id' => null
        //     ], 201);
        //    }
    }

    public function resendCode(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();
        $user->verification_code = rand(100000, 999999);

        if ($request->verify_by == 'email') {
            $user->notify(new EmailVerificationNotificationCode());
        } else {
            $otpController = new OTPVerificationController();
            $otpController->send_code($user);
        }

        $user->save();

        return response()->json([
            'result' => true,
            'message' => 'Verification code is sent again',
        ], 200);
    }

    public function confirmCode(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();

        // dd($request->verification_code, $request->user_id);
        if ($user->verification_code == $request->verification_code) {
            $user->email_verified_at = date('Y-m-d H:i:s');
            $user->verification_code = null;
            $user->save();
            return response()->json([
                'result' => true,
                'message' => 'Your account is now verified.',
            ], 200);
        } else {
            return response()->json([
                'result' => false,
                'message' => 'Code does not match, you can request for resending the code',
            ], 200);
        }
    }

    public function login(Request $request)
    {
        /*$request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);*/

        $user = User::whereIn('user_type', ['customer', 'seller'])->where('email', $request->email)->orWhere('phone', $request->email)->orWhere('phone', str_replace("+88","",$request->email))->first();
        if ($user != null) {
            if (Hash::check($request->password, $user->password)) {

                if ($user->email_verified_at == null) {
                    $tokenResult = $user->createToken('Personal Access Token');
                    $token = $tokenResult->token;
                    $token->expires_at = Carbon::now()->addWeeks(100);
                    $token->save();
                    return response()->json([
                        'result' => true,
                        'message' => 'Please verify your account',
                        'access_token' => $tokenResult->accessToken,
                        'token_type' => 'Bearer',
                        'expires_at' => Carbon::parse(
                            $tokenResult->token->expires_at
                        )->toDateTimeString(),
                        'user' => [
                            'id' => $user->id,
                            'type' => $user->user_type,
                            'name' => $user->name,
                            'email' => $user->email,
                            'avatar' => $user->avatar,
                            'avatar_original' => api_asset($user->avatar_original),
                            'phone' => $user->phone
                        ]
                    ]);
                }

                return $this->loginSuccess($user);

            } else {
                return response()->json(['result' => false, 'message' => 'Unauthorized', 'user' => null], 401);
            }
        } else {
            return response()->json(['result' => false, 'message' => 'User not found', 'user' => null], 401);
        }

    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'result' => true,
            'message' => 'Successfully logged out'
        ]);
    }

    public function socialLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email'
        ]);
        if (User::where('email', $request->email)->first() != null) {
            $user = User::where('email', $request->email)->first();
        } else {
            $user = new User([
                'name' => $request->name,
                'email' => $request->email,
                'provider_id' => $request->provider,
                'email_verified_at' => Carbon::now()
            ]);
            $user->save();
            $customer = new Customer;
            $customer->user_id = $user->id;
        
        $exists = Customer::where('area_code', '01')->orderBy('customer_id', 'desc')->get()->take(1);
            if (count($exists) > 0) {
                $customer_id = $exists[0]->customer_id;
                $customer_id++;
            }
            $customer->area_code = '01';
            $customer->created_from = 'App';
            $customer->customer_id = $customer_id;
            $customer->save();
        }
        // $tokenResult = $user->createToken('Personal Access Token');
        return $this->loginSuccess($user);
    }

    protected function loginSuccess($user, $token = null)
    {
        
        if (!$token) {
            $token = $user->createToken('API Token')->plainTextToken;
        }


        return response()->json([
            'result' => true,
            'message' => 'Successfully logged in',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => null,
            'user' => [
                'id' => $user->id,
                'type' => $user->user_type,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'avatar_original' => api_asset($user->avatar_original),
                'phone' => $user->phone
            ]
        ]);
    }
}
