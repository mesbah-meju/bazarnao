<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;
use App\Models\User;
use App\Models\Customer;
use App\Models\FinancialYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    /*protected $redirectTo = '/';*/


    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback(Request $request, $provider)
    {
        try {
            if ($provider == 'twitter') {
                $user = Socialite::driver('twitter')->user();
            } else {
                $user = Socialite::driver($provider)->stateless()->user();
            }
        } catch (\Exception $e) {
            flash("Something Went wrong. Please try again.")->error();
            return redirect()->route('user.login');
        }

        // check if they're an existing user
        $existingUser = User::where('provider_id', $user->id)->orWhere('email', $user->email)->first();

        if ($existingUser) {
            // log them in
            Auth::login($existingUser, true);
        } else {
            // create a new user
            $newUser                  = new User;
            $newUser->name            = $user->name;
            $newUser->email           = $user->email;
            $newUser->email_verified_at = date('Y-m-d H:m:s');
            $newUser->provider_id     = $user->id;
            $newUser->save();

            $exists = Customer::where('area_code', '02')->orderBy('customer_id', 'desc')->get()->take(1);
            if (count($exists) > 0) {
                $customer_id = $exists[0]->customer_id;
                $customer_id = $customer_id + 1;
                if (strlen((string)$customer_id) < 8) {
                    $customer_id = '0' . $customer_id;
                }
            } else {
                $customer_id = '02000001';
            }

            $customer = new Customer;
            $customer->user_id = $newUser->id;
            $customer->created_from = 'Web';
            $customer->area_code = '02';
            $customer->customer_id = $customer_id;
            $customer->save();

            Auth::login($newUser, true);
        }
        if (session('link') != null) {
            return redirect(session('link'));
        } else {
            return redirect()->route('dashboard');
        }
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        if (filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)) {
            return $request->only($this->username(), 'password');
        }
        return ['phone' => $request->get('email'), 'password' => $request->get('password')];
    }

    /**
     * Check user's role and redirect user based on their role
     * @return
     */
    public function authenticated()
    {
        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {

            // Get the active financial year for today's date
            $financial_year = FinancialYear::where('start_date', '<=', date("Y-m-d"))
                ->where('end_date', '>=', date("Y-m-d"))
                ->where("status", 1)
                ->first();

            // If no financial year found for today, get any active financial year
            if (!$financial_year) {
                $financial_year = FinancialYear::where('status', 1)
                    ->where('is_close', 0)
                    ->orderBy('id', 'DESC')
                    ->first();
            }

            // Set financial year session only if found
            if ($financial_year) {
                Session::put('fyear', $financial_year->id);
                Session::put('fyearName', $financial_year->year_name);
                Session::put('fyearStartDate', $financial_year->start_date);
                Session::put('fyearEndDate', $financial_year->end_date);
            } else {
                // Log warning if no financial year found
                \Log::warning('No active financial year found during login for user: ' . Auth::user()->id);
            }

            if (Auth::user()->user_type == 'staff') {
                if (Auth::user()->staff->role->role_type == 1)
                    return redirect()->route('admin.dashboard');
                else
                    return redirect()->route('staff.dashboard');
            } else
                return redirect()->route('admin.dashboard');
        } else {

            if (session('link') != null) {
                return redirect(session('link'));
            } else {
                return redirect()->route('home');
            }
        }
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        flash(translate('Invalid email or password'))->error();
        return back();
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        if (session('admin_login')) {
            session()->forget('admin_login');
        }

        if (Auth::user() != null && (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff')) {
            $redirect_route = 'login';
        } else {
            $redirect_route = 'home';
        }

        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect()->route($redirect_route);
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
