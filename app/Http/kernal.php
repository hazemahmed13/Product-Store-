<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware will run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\SetCacheHeaders::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\Session\Middleware\AuthenticateSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\CheckIfLoggedIn::class,  // إضافة Middleware الذي تحتاجه
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'checklogin' => \App\Http\Middleware\CheckIfLoggedIn::class,  // هنا Middleware الذي تم إنشاؤه
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        // يمكن إضافة المزيد من الـ Middleware حسب الحاجة
    ];
}
