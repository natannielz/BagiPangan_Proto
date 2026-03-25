<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Notifications\DonationApprovedNotification;
use App\Notifications\DonationRejectedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DonationModerationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $query = Donation::query()
            ->with(['donor', 'category'])
            ->orderByRaw("CASE moderation_status WHEN 'pending' THEN 0 WHEN 'approved' THEN 1 ELSE 2 END")
            ->latest();

        if (in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('moderation_status', $status);
        }

        $donations = $query->paginate(20)->withQueryString();

        return view('admin.donations', [
            'donations'    => $donations,
            'statusFilter' => $status,
        ]);
    }

    public function approve(Donation $donation): RedirectResponse
    {
        if ($donation->moderation_status !== 'pending') {
            return back()->with('error', 'Donasi ini tidak dalam status pending.');
        }

        $donation->update(['moderation_status' => 'approved']);

        $donation->donor?->notify(new DonationApprovedNotification($donation));

        return back()->with('success', 'Donasi berhasil disetujui.');
    }

    public function reject(Request $request, Donation $donation): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        if ($donation->moderation_status !== 'pending') {
            return back()->with('error', 'Donasi ini tidak dalam status pending.');
        }

        if (in_array($donation->status, ['claimed', 'picked_up', 'completed'], true)) {
            return back()->with('error', 'Tidak bisa menolak donasi yang sudah diklaim.');
        }

        $donation->update([
            'moderation_status' => 'rejected',
            'rejection_reason'  => $request->rejection_reason,
            'status'            => 'cancelled',
        ]);

        $donation->donor?->notify(new DonationRejectedNotification($donation));

        return back()->with('success', 'Donasi berhasil ditolak.');
    }
}
