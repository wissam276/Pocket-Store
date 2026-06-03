<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CompanyMiddleware;
use App\Http\Middleware\AdminMiddleware; // 1. استدعاء الممرر الجديد هنا

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // 2. تسجيل الـ Aliases للممررين معاً
        $middleware->alias([
            'company' => CompanyMiddleware::class,
            'admin'   => AdminMiddleware::class, // أضفنا هذا السطر هنا
        ]);

        // إرجاع استجابة JSON بدلاً من التوجيه لصفحة الـ login عند فشل الـ Token
        $middleware->redirectGuestsTo(fn () => response()->json([
            'message' => 'Unauthenticated. Please provide a valid Bearer Token.'
        ], 401));

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
