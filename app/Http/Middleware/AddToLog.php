<?php

namespace App\Http\Middleware;

use Closure;
use App\LogActivity;

class AddToLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
       // if (request()->route()) {
            if (auth()->user()) {
                LogActivity::create([
                    'url' => request()->fullUrl(),
                    'ip' => request()->ip(),
                    'agent' => request()->userAgent(),
                    'user_id' => auth()->id(),
                ]);
            } else {
                LogActivity::create([
                    'url' => request()->fullUrl(),
                    'ip' => request()->ip(),
                    'agent' => request()->userAgent(),
                    'user_id' => 0,
                ]);
            }
      //  }
        return $next($request);
    }
}
