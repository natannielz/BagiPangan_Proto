<?php

namespace App\Http\Controllers\Receiver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $claims = $request->user()->claims();

        return view('receiver.dashboard', [
            'totalClaims'     => $claims->count(),
            'activeClaims'    => (clone $claims)->where('status', 'claimed')->count(),
            'pendingClaims'   => (clone $claims)->whereIn('status', ['claimed', 'awaiting_confirmation'])->count(),
            'completedClaims' => (clone $claims)->where('status', 'completed')->count(),
        ]);
    }
}
