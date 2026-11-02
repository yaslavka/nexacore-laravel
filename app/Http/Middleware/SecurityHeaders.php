<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('X-Frame-Options', 'DENY');

        $isAdmin = $request->is('admin') || $request->is('admin/*') || $request->is('livewire/*') || $request->is('filament/*');
        if (! $isAdmin) {
            $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; font-src 'self'; connect-src 'self'; object-src 'none'; base-uri 'self'; frame-ancestors 'none'; form-action 'self'");
        }

        if ($request->is('checkout/*') || $request->is('*/checkout/*')) {
            $response->headers->set('Referrer-Policy', 'no-referrer');
        }

        return $response;
    }
}
