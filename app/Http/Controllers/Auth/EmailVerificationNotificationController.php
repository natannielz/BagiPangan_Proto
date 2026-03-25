<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            $redirectTo = match ($request->user()->role) {
                'admin' => '/admin/dashboard',
                'donor' => '/donor/dashboard',
                'receiver' => '/receiver/dashboard',
                default => '/donations',
            };

            return redirect()->intended($redirectTo);
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
