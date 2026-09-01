<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        // Admin Panel Access Control
        if (in_array($user->role, ['admin', 'manager'])) {
            $request->session()->regenerate();
            // return redirect()->intended(route('admin.dashboard', absolute: false));
            return redirect()->route('admin.dashboard');

        }

        // Driver panel Access Control - optional
        if (in_array($user->role, ['driver', 'deliveryman'])) {
            $request->session()->regenerate();
            // return redirect()->intended(route('driver.dashboard', absolute: false));
            return redirect()->route('driver.dashboard');
        }

        // Fallback (unknown role)
        Auth::logout();
        return back()->withErrors(['email' => 'You are not authorized to access the admin panel.',]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
