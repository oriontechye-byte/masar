<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * Rate limiter لحماية محاولات الدخول وواجهات الـ API.
         *
         * - 'login'  : يقيّد محاولات تسجيل الدخول (Brute-force protection).
         * - 'api'    : يقيّد طلبات الـ API لكل مستخدم أو IP.
         * - 'admin'  : (اختياري) حد مختلف لمسارات الإدارة لو أردت تمييزها.
         *
         * لاحقًا يجب ربط 'throttle:login' على POST /login في routes/web.php
         * و 'throttle:api' على مسارات الـ API في routes/api.php
         */

        // 1) حد محاولات تسجيل الدخول: 5 محاولات/دقيقة لكل بريد أو IP
        RateLimiter::for('login', function (Request $request) {
            // نستخدم الإيميل إذا موجود، وإلا نستخدم IP
            $key = (string) (strtolower($request->input('email')) ?: $request->ip());

            return Limit::perMinute(5)->by($key)->response(function () {
                // رد موحّد عند تجاوز الحد — يعطي HTTP 429
                return response()->json([
                    'message' => 'عدد محاولات تسجيل الدخول كثير. حاول لاحقًا بعد قليل.',
                ], 429);
            });
        });

        // 2) حد واجهات الـ API: 60 طلب/دقيقة لكل مستخدم (أو IP لو غير مسجل)
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(
                optional($request->user())->id ?: $request->ip()
            );
        });

        // 3) حد خاص للمسارات الإدارية (اختياري) — 30 طلب/دقيقة
        RateLimiter::for('admin', function (Request $request) {
            return Limit::perMinute(30)->by(
                optional($request->user())->id ?: $request->ip()
            );
        });
    }
}
