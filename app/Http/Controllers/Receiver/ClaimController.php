<?php

namespace App\Http\Controllers\Receiver;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClaimProofRequest;
use App\Events\DonationClaimed;
use App\Models\Claim;
use App\Models\Donation;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Throwable;

class ClaimController extends Controller
{
    public function index(Request $request): View
    {
        $claims = Claim::query()
            ->where('claimer_id', $request->user()->id)
            ->with(['donation.category'])
            ->latest()
            ->paginate(15);

        return view('receiver.claims', [
            'claims' => $claims,
        ]);
    }

    public function store(Request $request, Donation $donation): RedirectResponse
    {
        $this->authorize('create', Claim::class);

        try {
            $claim = DB::transaction(function () use ($donation, $request) {
                $lockedDonation = Donation::lockForUpdate()->findOrFail($donation->id);

                if ($lockedDonation->moderation_status !== 'approved') {
                    return 'Donasi ini belum disetujui moderator.';
                }

                if ($lockedDonation->expiry_at && $lockedDonation->expiry_at->isPast()) {
                    return 'Donasi ini sudah kadaluarsa.';
                }

                if ($lockedDonation->status !== 'available') {
                    return 'Sudah diklaim oleh penerima lain.';
                }

                $activeClaims = Claim::query()
                    ->where('claimer_id', $request->user()->id)
                    ->whereIn('status', ['claimed', 'awaiting_confirmation'])
                    ->count();

                if ($activeClaims >= 3) {
                    return 'Batas klaim aktif tercapai (maksimal 3).';
                }

                $lockedDonation->update(['status' => 'claimed']);

                return Claim::create([
                    'donation_id' => $lockedDonation->id,
                    'claimer_id' => $request->user()->id,
                    'claimed_at' => now(),
                    'status' => 'claimed',
                ]);
            });
        } catch (QueryException $e) {
            return back()->with('error', 'Sudah diklaim oleh penerima lain.');
        }

        if (is_string($claim)) {
            return back()->with('error', $claim);
        }

        DonationClaimed::dispatch($claim);

        return back()->with('success', 'Berhasil diklaim! Donasi ini sekarang milik Anda.');
    }

    public function proofForm(Claim $claim): View
    {
        $this->authorize('uploadProof', $claim);

        $claim->load('donation.category');

        return view('receiver.claims-proof', [
            'claim' => $claim,
        ]);
    }

    public function uploadProof(ClaimProofRequest $request, Claim $claim): RedirectResponse
    {
        $this->authorize('uploadProof', $claim);

        if ($claim->status === 'completed') {
            abort(409);
        }

        $file = $request->file('proof_photo');

        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getPathname())->scaleDown(width: 800);
            $encoded = $image->toWebp(quality: 80);
        } catch (Throwable $e) {
            abort(422, 'Invalid image');
        }

        $path = 'claims/'.$claim->id.'/'.Str::uuid().'.webp';

        if ($claim->proof_photo_path) {
            Storage::disk('local')->delete($claim->proof_photo_path);
        }

        Storage::disk('local')->put($path, (string) $encoded);

        $claim->forceFill([
            'proof_photo_path' => $path,
            'proof_uploaded_at' => now(),
            'status' => 'awaiting_confirmation',
        ])->save();

        return Redirect::route('receiver.claims')->with('status', 'proof-uploaded');
    }
}
