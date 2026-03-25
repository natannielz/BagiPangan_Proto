<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotSuspended
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($user->suspended_at === null) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'ACCOUNT_SUSPENDED',
                'message' => 'Akun Anda sedang dinonaktifkan sementara.',
            ], 403);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('suspended');
    }
}
