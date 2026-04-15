<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLoginAttempt;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(Request $request): View|RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $ip = $request->ip();

        // Attempt tracking is best-effort — if the admin_login_attempts
        // table is missing (fresh deploy pre-migration) we don't want
        // login to 500. Skip lockout check in that case.
        try {
            if (AdminLoginAttempt::isLockedOut($ip)) {
                throw ValidationException::withMessages([
                    'email' => 'Too many failed attempts. Please try again in 15 minutes.',
                ]);
            }
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable) {
            // Table missing or DB error — skip throttling gracefully.
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = (bool) $request->boolean('remember');

        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            try {
                $user = Auth::guard('admin')->user();
                $user->last_login_at = now();
                $user->last_login_ip = $ip;
                $user->save();
            } catch (\Throwable) {
                // Timestamp update is non-critical — don't block the login.
            }

            try {
                AdminLoginAttempt::create([
                    'ip' => $ip,
                    'email' => $credentials['email'],
                    'success' => true,
                    'user_agent' => substr((string) $request->userAgent(), 0, 500),
                    'attempted_at' => now(),
                ]);
            } catch (\Throwable) {
                // Skip attempt log if table missing.
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        try {
            AdminLoginAttempt::create([
                'ip' => $ip,
                'email' => $credentials['email'],
                'success' => false,
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'attempted_at' => now(),
            ]);
        } catch (\Throwable) {
            // Skip attempt log if table missing.
        }

        throw ValidationException::withMessages([
            'email' => 'These credentials do not match our records.',
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
