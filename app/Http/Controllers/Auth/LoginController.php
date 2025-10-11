<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * عرض صفحة تسجيل دخول المسؤول.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * معالجة طلب تسجيل الدخول.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            // السماح للمسؤولين فقط
            if ((Auth::user()->role ?? null) !== 'admin') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'هذه الصفحة مخصصة للمسؤولين فقط.',
                ]);
            }

            $request->session()->regenerate();
            return redirect()->intended('/admin/dashboard');
        }

        return back()->withErrors([
            'email' => 'البيانات المدخلة غير صحيحة.',
        ]);
    }

    /**
     * تسجيل خروج المستخدم.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/login');
    }
}
