<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckIfLoggedIn
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // إذا لم يكن المستخدم قد سجل دخوله
        if (!Auth::check()) {
            // إعادة توجيه المستخدم إلى صفحة تسجيل الدخول
            return redirect()->route('login');
        }

        return $next($request);
    }
}
