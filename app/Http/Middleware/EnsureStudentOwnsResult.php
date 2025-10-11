<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureStudentOwnsResult
{
    public function handle(Request $request, Closure $next): Response
    {
        // الإدمن مسموح له دائمًا
        if (Auth::check() && (Auth::user()->role ?? null) === 'admin') {
            return $next($request);
        }

        // حاول التقاط معرف من الراوت إن وُجد
        $routeId = $request->route('student_id')
            ?? $request->route('student')
            ?? $request->route('id');

        // خُذ معرّف الطالب من الجلسة (المسموح له بالمشاهدة)
        $viewerId = (int) (
            $request->session()->get('viewer_student_id')
            ?? $request->session()->get('student_id_for_test')
            ?? $request->session()->get('student_id')
            ?? 0
        );

        // 1) لا يوجد ID في الرابط: اترك التحقق للكنترولر (سيقرأ من الجلسة وي abort إذا مفقود)
        if (is_null($routeId)) {
            return $next($request);
        }

        // 2) يوجد ID في الرابط: لا بد من تطابقه مع الجلسة
        $routeId = (int) $routeId;
        if ($routeId > 0 && $viewerId > 0 && $routeId === $viewerId) {
            return $next($request);
        }

        abort(403, 'غير مصرح لك بعرض هذه النتائج.');
    }
}
