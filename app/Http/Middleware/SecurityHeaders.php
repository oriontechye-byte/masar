<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // رؤوس أمان أساسية
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'no-referrer');
        $response->headers->set('Permissions-Policy', "geolocation=(), camera=(), microphone=()");

        // HSTS في الإنتاج وعلى HTTPS فقط
        if ($request->isSecure() && app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // ✅ CSP متساهلة بالقدر اللازم لعمل الصفحات الحالية:
        // - نسمح بـ inline scripts/styles لأن صفحاتك تستخدم سكربت داخل الصفحة.
        // - نسمح بخطوط Google و cdnjs/jsdelivr و fontawesome.
        $cspParts = [
            "default-src 'self'",
            "base-uri 'self'",
            "frame-ancestors 'none'",
            "form-action 'self'",

            // سكربتات: ذاتية + inline + CDNات شائعة تستخدمها
            "script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net",

            // أنماط: ذاتية + inline + Google Fonts + CDNات
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net",

            // صور: من نفس النطاق + data: + blob:
            "img-src 'self' data: blob:",

            // خطوط: من نفس النطاق + Google Fonts + cdnjs
            "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com",

            // اتصالات XHR/Fetch
            "connect-src 'self'",

            // حظر الكائنات (Flash, etc)
            "object-src 'none'",
        ];
        $response->headers->set('Content-Security-Policy', implode('; ', $cspParts));

        // تعزيز أمان الكوكيز
        if ($request->isSecure()) {
            ini_set('session.cookie_secure', '1');
        }
        ini_set('session.cookie_httponly', '1');

        return $response;
    }
}
