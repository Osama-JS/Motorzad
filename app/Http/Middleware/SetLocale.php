<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->has('lang') && in_array($request->query('lang'), ['ar', 'en'])) {
            $locale = $request->query('lang');
            Session::put('locale', $locale);
            App::setLocale($locale);
        } elseif ($request->has('locale') && in_array($request->query('locale'), ['ar', 'en'])) {
            $locale = $request->query('locale');
            Session::put('locale', $locale);
            App::setLocale($locale);
        } elseif (Session::has('locale')) {
            $locale = Session::get('locale');
            App::setLocale($locale);
        } else {
            // Default to arabic
            App::setLocale('ar');
        }

        return $next($request);
    }
}
