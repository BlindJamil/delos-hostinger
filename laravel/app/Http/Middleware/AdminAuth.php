<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Protect admin routes. Redirects to login if not authenticated.
     * Idle timeout tracks config('session.lifetime') so we never expire
     * an admin ahead of the framework's own session window.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check()) {
            return $request->expectsJson()
                ? response()->json(['error' => 'Unauthenticated.'], 401)
                : redirect()->route('admin.login');
        }

        // Match the framework's configured session lifetime so a long edit
        // (e.g. home-page form with 278+ fields) never triggers a CSRF
        // regeneration mid-flow. AppServiceProvider forces this to 480 min.
        $idleLimitMinutes = (int) (config('session.lifetime') ?? 120);
        $lastActivity = $request->session()->get('admin_last_activity');
        if ($lastActivity && now()->diffInMinutes($lastActivity) > $idleLimitMinutes) {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('admin.login')->with('status', 'Session expired. Please log in again.');
        }

        $request->session()->put('admin_last_activity', now());

        return $next($request);
    }
}
