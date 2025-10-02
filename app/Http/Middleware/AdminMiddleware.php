<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and has admin privileges
        // For now, we'll use the Filament admin check
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();

        // Check if user can access Filament admin panel (admin users)
        if (!$user->canAccessPanel(\Filament\Facades\Filament::getDefaultPanel())) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}
