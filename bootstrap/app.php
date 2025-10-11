<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',

        // لو لا تستخدم API أو الملف غير موجود، علّق السطر التالي:
        // api: __DIR__ . '/../routes/api.php',

        // فعِّل السطر التالي فقط إذا ملف routes/api.php موجود فعلاً:
        api: __DIR__ . '/../routes/api.php',

        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ===== Aliases =====
        $middleware->alias([
            'auth.redirect' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            // منع IDOR على صفحات نتائج الطلاب
            'owns.result'   => \App\Http\Middleware\EnsureStudentOwnsResult::class,
            // لو فعلاً بتقفل لوحة الإدارة على دور admin فقط
            'auth.admin'    => \App\Http\Middleware\EnsureAdmin::class,
        ]);

        // ===== Global Security =====
        // لا تستخدم app()->environment() هنا (حتى لا يظهر Target class [env] does not exist)
        $isProd = (($_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? 'local') === 'production');

        if ($isProd) {
            // فرض HTTPS في الإنتاج
            $middleware->append(\App\Http\Middleware\ForceHttps::class);
        }

        // رؤوس الأمان + CSP
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        // ===========================
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
