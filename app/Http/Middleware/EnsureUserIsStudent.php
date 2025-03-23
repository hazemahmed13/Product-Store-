<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsStudent
{
    public function handle(Request $request, Closure $next)
    {
        // التحقق من أن المستخدم طالب
        if (auth()->user()->role !== 'student') {
            abort(403, 'Unauthorized action.'); // 403 Forbidden
        }

        return $next($request);
    }
}