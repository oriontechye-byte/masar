<?php

namespace App\Http\Middleware; // This is the line that has been corrected

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('student_id')) {
            // If the student is already logged in, redirect them to the test page
            return redirect('/test?student_id=' . session('student_id'));
        }

        // If not logged in, allow them to see the page (e.g., login or register)
        return $next($request);
        
    }
}