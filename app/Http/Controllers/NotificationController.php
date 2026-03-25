<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $notifications = $user->notifications()->latest()->paginate(20);
        $unreadCount = $user->unreadNotifications()->count();

        return view('notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function markRead(Request $request, string $notification): RedirectResponse
    {
        $user = $request->user();

        $notif = DatabaseNotification::query()
            ->where('id', $notification)
            ->where('notifiable_id', $user->id)
            ->firstOrFail();

        $notif->markAsRead();

        return Redirect::route('notifications.index');
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return Redirect::route('notifications.index');
    }
}

