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

        // Block if locked out
        if (AdminLoginAttempt::isLockedOut($ip)) {
            throw ValidationException::withMessages([
                'email' => 'Too many failed attempts. Please try again in 15 minutes.',
            ]);
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = (bool) $request->boolean('remember');

        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::guard('admin')->user();
            $user->last_login_at = now();
            $user->last_login_ip = $ip;
            $user->save();

            AdminLoginAttempt::create([
                'ip' => $ip,
                'email' => $credentials['email'],
                'success' => true,
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'attempted_at' => now(),
            ]);

            return redirect()->intended(route('admin.dashboard'));
        }

        AdminLoginAttempt::create([
            'ip' => $ip,
            'email' => $credentials['email'],
            'success' => false,
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'attempted_at' => now(),
        ]);

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
