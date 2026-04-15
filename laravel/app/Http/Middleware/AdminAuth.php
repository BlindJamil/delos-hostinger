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
     * Also enforces 2-hour idle session timeout.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check()) {
            return $request->expectsJson()
                ? response()->json(['error' => 'Unauthenticated.'], 401)
                : redirect()->route('admin.login');
        }

        // Idle timeout: 2 hours
        $lastActivity = $request->session()->get('admin_last_activity');
        if ($lastActivity && now()->diffInMinutes($lastActivity) > 120) {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('admin.login')->with('status', 'Session expired. Please log in again.');
        }

        $request->session()->put('admin_last_activity', now());

        return $next($request);
    }
}
