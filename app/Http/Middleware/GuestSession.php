<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class GuestSession
{
    /**
     * Handle an incoming request.
     *
     * Generates an anonymous UUID for guest users who are not authenticated.
     * This allows anonymous screening access without requiring login.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() && ! $request->session()->has('guest_id')) {
            $request->session()->put('guest_id', Str::uuid()->toString());
        }

        return $next($request);
    }
}
