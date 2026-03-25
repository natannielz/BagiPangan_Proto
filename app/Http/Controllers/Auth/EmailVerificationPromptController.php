<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        if (! $request->user()->hasVerifiedEmail()) {
            return view('auth.verify-email');
        }

        $redirectTo = match ($request->user()->role) {
            'admin' => '/admin/dashboard',
            'donor' => '/donor/dashboard',
            'receiver' => '/receiver/dashboard',
            default => '/donations',
        };

        return redirect()->intended($redirectTo);
    }
}
