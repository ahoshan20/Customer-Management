<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Enums\ActiveInactive;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    public function login(Request $request): Response|RedirectResponse
    {
        if ($request->isMethod('get')) {
            if(Auth::guard('admin')->check()) {
                return redirect()->route('admin.dashboard');
            }
            return Inertia::render('admin/auth/Login');
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $adminGuard = Auth::guard('admin');

        if (! $adminGuard->attempt($credentials, (bool) $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();
        $user = $adminGuard->user();
        $admin = $user?->admin;

        if ($admin === null || $admin->status !== ActiveInactive::ACTIVE) {
            $adminGuard->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => __('You are not allowed to access the admin panel.'),
            ]);
        }

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
