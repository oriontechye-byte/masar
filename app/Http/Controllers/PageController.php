<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PageController extends Controller
{
    /**
     * عرض الصفحة الرئيسية للمشروع.
     */
    public function showLandingPage()
    {
        return view('landing');
    }

    /**
     * تبديل ثيم الموقع (فاتح/داكن) وحفظه في الجلسة.
     *
     * @param string $theme
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchTheme($theme)
    {
        if (in_array($theme, ['light', 'dark'])) {
            Session::put('theme', $theme);
        }
        return back();
    }
}