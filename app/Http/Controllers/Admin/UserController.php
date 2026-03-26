<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $role = $request->string('role')->toString();

        $query = User::query()
            ->when(in_array($role, ['admin', 'donor', 'receiver'], true), fn ($q) => $q->where('role', $role))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users', [
            'users'      => $query,
            'roleFilter' => $role,
        ]);
    }

    public function suspend(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menangguhkan akun Anda sendiri.');
        }

        if ($user->suspended_at) {
            return back()->with('error', 'Akun ini sudah ditangguhkan.');
        }

        $user->update(['suspended_at' => now()]);

        AuditLog::create([
            'user_id'      => Auth::id(),
            'action'       => 'user.suspended',
            'subject_type' => User::class,
            'subject_id'   => $user->id,
            'payload'      => ['name' => $user->name, 'email' => $user->email],
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
        ]);

        return back()->with('success', 'Akun ditangguhkan.');
    }

    public function unsuspend(User $user): RedirectResponse
    {
        if (! $user->suspended_at) {
            return back()->with('error', 'Akun ini tidak dalam status ditangguhkan.');
        }

        $user->update(['suspended_at' => null]);

        AuditLog::create([
            'user_id'      => Auth::id(),
            'action'       => 'user.unsuspended',
            'subject_type' => User::class,
            'subject_id'   => $user->id,
            'payload'      => ['name' => $user->name, 'email' => $user->email],
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
        ]);

        return back()->with('success', 'Akun diaktifkan kembali.');
    }
}
