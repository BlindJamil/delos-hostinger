<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminUserRequest;
use App\Models\AdminUser;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminUserController extends Controller implements HasMiddleware
{
    /**
     * Guard every action in this controller behind a super-admin check.
     * Laravel 11+ removed $this->middleware() from controllers, so we declare
     * middleware via the HasMiddleware interface.
     */
    public static function middleware(): array
    {
        return [
            function (Request $request, Closure $next) {
                if (!auth('admin')->check() || !auth('admin')->user()->is_super) {
                    abort(403, 'Only super admins can manage admin users.');
                }
                return $next($request);
            },
        ];
    }

    public function index(): View
    {
        $admins = AdminUser::query()
            ->orderBy('is_super', 'desc')
            ->orderBy('name')
            ->get();

        return view('admin.admin-users.index', compact('admins'));
    }

    public function create(): View
    {
        $adminUser = new AdminUser(['is_super' => false]);
        return view('admin.admin-users.create', compact('adminUser'));
    }

    public function store(AdminUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        AdminUser::create($data);

        return redirect()
            ->route('admin.admin-users.index')
            ->with('success', 'Admin user created.');
    }

    public function edit(AdminUser $adminUser): View
    {
        return view('admin.admin-users.edit', compact('adminUser'));
    }

    public function update(AdminUserRequest $request, AdminUser $adminUser): RedirectResponse
    {
        $data = $request->validated();

        // If password field left blank on update, keep the existing hash.
        if (empty($data['password'])) {
            unset($data['password']);
        }

        // Prevent the currently-logged-in super admin from removing their own
        // super flag — that would lock them out of this page.
        if ($adminUser->id === auth('admin')->id() && !($data['is_super'] ?? false)) {
            throw ValidationException::withMessages([
                'is_super' => 'You cannot remove super-admin from your own account.',
            ]);
        }

        $adminUser->update($data);

        return redirect()
            ->route('admin.admin-users.edit', $adminUser)
            ->with('success', 'Changes saved.');
    }

    public function destroy(AdminUser $adminUser): RedirectResponse
    {
        if ($adminUser->id === auth('admin')->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Safety net: never let the system end up with zero super admins.
        if ($adminUser->is_super && AdminUser::where('is_super', true)->count() <= 1) {
            return back()->with('error', 'Cannot delete the only super admin — promote another admin first.');
        }

        $adminUser->delete();

        return redirect()
            ->route('admin.admin-users.index')
            ->with('success', 'Admin user deleted.');
    }

    /** Dedicated password reset — doesn't require touching other fields. */
    public function resetPassword(Request $request, AdminUser $adminUser): RedirectResponse
    {
        $request->validate([
            'password' => [
                'required', 'string',
                Password::min(12)->letters()->numbers()->symbols(),
                'confirmed',
            ],
        ]);

        $adminUser->update(['password' => $request->input('password')]);

        return back()->with('success', "Password reset for {$adminUser->email}.");
    }
}
