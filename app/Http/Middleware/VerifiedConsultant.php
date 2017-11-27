<?php

namespace newlifecfo\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class VerifiedConsultant
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
        if (!Auth::user()->isVerifiedConsultant()){
            return redirect('pending');
        }
        return $next($request);
    }
}
