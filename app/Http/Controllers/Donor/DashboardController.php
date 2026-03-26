<?php

namespace App\Http\Controllers\Donor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $donations = $request->user()->donations();

        return view('donor.dashboard', [
            'totalDonations'     => $donations->count(),
            'activeDonations'    => (clone $donations)->where('status', 'available')->count(),
            'claimedDonations'   => (clone $donations)->where('status', 'claimed')->count(),
            'completedDonations' => (clone $donations)->where('status', 'completed')->count(),
        ]);
    }
}
