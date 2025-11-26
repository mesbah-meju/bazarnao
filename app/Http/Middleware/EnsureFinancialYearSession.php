<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\FinancialYear;

class EnsureFinancialYearSession
{
    /**
     * Handle an incoming request.
     * 
     * This middleware ensures that financial year session is always set
     * for authenticated admin and staff users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Only check for admin and staff users
        if (Auth::check() && (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff')) {
            
            // Check if financial year session is missing or empty
            if (empty(Session::get('fyear')) || empty(Session::get('fyearStartDate'))) {
                
                // Try to get the active financial year for today's date
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

                // Set financial year session if found
                if ($financial_year) {
                    Session::put('fyear', $financial_year->id);
                    Session::put('fyearName', $financial_year->year_name);
                    Session::put('fyearStartDate', $financial_year->start_date);
                    Session::put('fyearEndDate', $financial_year->end_date);
                }
            }
        }

        return $next($request);
    }
}

