<?php

namespace App\Http\Controllers\Donor;

use App\Http\Controllers\Controller;
use App\Events\DonationCompleted;
use App\Models\Claim;
use App\Models\Donation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ClaimController extends Controller
{
    public function index(Request $request): View
    {
        $claims = Claim::query()
            ->whereHas('donation', fn ($q) => $q->where('donor_id', $request->user()->id))
            ->where('status', 'awaiting_confirmation')
            ->with(['donation.category', 'claimer'])
            ->latest()
            ->paginate(15);

        return view('donor.claims', [
            'claims' => $claims,
        ]);
    }

    public function verify(Request $request, Claim $claim): RedirectResponse
    {
        $claim->load('donation');
        $this->authorize('verify', $claim);

        if (! $claim->proof_uploaded_at || ! $claim->proof_photo_path) {
            abort(422, 'Proof required');
        }

        DB::transaction(function () use ($claim, $request) {
            $lockedClaim = Claim::lockForUpdate()->findOrFail($claim->id);
            $lockedDonation = Donation::lockForUpdate()->findOrFail($lockedClaim->donation_id);

            if ($lockedClaim->status !== 'awaiting_confirmation') {
                abort(409);
            }

            $lockedClaim->forceFill([
                'verified_at' => now(),
                'verifier_id' => $request->user()->id,
                'status' => 'completed',
            ])->save();

            $lockedDonation->forceFill(['status' => 'completed'])->save();
        });

        DonationCompleted::dispatch($claim->fresh());

        return Redirect::route('donor.claims.index')->with('status', 'claim-verified');
    }
}
