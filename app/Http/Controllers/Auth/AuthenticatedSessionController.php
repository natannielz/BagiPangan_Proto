<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
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
        $validated = $request->validated();

        $request->ensureIsNotRateLimited();

        $user = User::query()->where('email', $validated['email'])->first();

        if ($user?->suspended_at !== null) {
            return back()
                ->withInput($request->only('email'))
                ->with('toast', 'Akun Anda sedang dinonaktifkan sementara.')
                ->withErrors(['email' => 'Akun Anda sedang dinonaktifkan sementara.']);
        }

        if (! Auth::attempt($validated, $request->boolean('remember'))) {
            RateLimiter::hit($request->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($request->throttleKey());

        $request->session()->regenerate();

        $redirectTo = match ($request->user()->role) {
            'admin' => '/admin/dashboard',
            'donor' => '/donor/dashboard',
            'receiver' => '/receiver/dashboard',
            default => '/donations',
        };

        return redirect()->intended($redirectTo);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
