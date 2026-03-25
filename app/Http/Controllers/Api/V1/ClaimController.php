<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Events\DonationClaimed;
use App\Events\DonationCompleted;
use App\Http\Requests\ClaimProofRequest;
use App\Http\Resources\ClaimResource;
use App\Http\Responses\ApiError;
use App\Models\Claim;
use App\Models\Donation;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Throwable;

class ClaimController extends Controller
{
    public function claimDonation(Request $request, Donation $donation): JsonResponse
    {
        $this->authorize('create', Claim::class);

        try {
            $claim = DB::transaction(function () use ($donation, $request) {
                $lockedDonation = Donation::lockForUpdate()->findOrFail($donation->id);

                if ($lockedDonation->moderation_status !== 'approved') {
                    return ApiError::respond('NOT_APPROVED', 'Donasi ini belum disetujui moderator.', 422);
                }

                if ($lockedDonation->expiry_at && $lockedDonation->expiry_at->isPast()) {
                    return ApiError::respond('DONATION_EXPIRED', 'Donasi ini sudah kadaluarsa.', 422);
                }

                if ($lockedDonation->status !== 'available') {
                    return ApiError::respond('ALREADY_CLAIMED', 'Donasi ini sudah diklaim.', 409);
                }

                $activeClaims = Claim::query()
                    ->where('claimer_id', $request->user()->id)
                    ->whereIn('status', ['claimed', 'awaiting_confirmation'])
                    ->count();

                if ($activeClaims >= 3) {
                    return ApiError::respond('CLAIM_LIMIT_REACHED', 'Anda sudah mencapai batas maksimal 3 klaim aktif.', 422);
                }

                $lockedDonation->update(['status' => 'claimed']);

                return Claim::create([
                    'donation_id' => $lockedDonation->id,
                    'claimer_id'  => $request->user()->id,
                    'claimed_at'  => now(),
                    'status'      => 'claimed',
                ]);
            });
        } catch (QueryException $e) {
            return ApiError::respond('ALREADY_CLAIMED', 'Donasi ini sudah diklaim.', 409);
        }

        // If transaction returned an error response, return it
        if ($claim instanceof JsonResponse) {
            return $claim;
        }

        DonationClaimed::dispatch($claim);

        return response()->json([
            'data'    => new ClaimResource($claim),
            'message' => 'OK',
        ], 201);
    }

    public function uploadProof(ClaimProofRequest $request, Claim $claim): JsonResponse
    {
        $this->authorize('uploadProof', $claim);

        if ($claim->status === 'completed') {
            return ApiError::respond('INVALID_STATE', 'Klaim ini sudah selesai.', 409);
        }

        $file = $request->file('proof_photo');

        try {
            $manager = new ImageManager(new Driver());
            $image   = $manager->read($file->getPathname())->scaleDown(width: 800);
            $encoded = $image->toWebp(quality: 80);
        } catch (Throwable $e) {
            return ApiError::respond('INVALID_STATE', 'File gambar tidak valid.', 422);
        }

        $path = 'claims/'.$claim->id.'/'.Str::uuid().'.webp';

        if ($claim->proof_photo_path) {
            Storage::disk('local')->delete($claim->proof_photo_path);
        }

        Storage::disk('local')->put($path, (string) $encoded);

        $claim->forceFill([
            'proof_photo_path'  => $path,
            'proof_uploaded_at' => now(),
            'status'            => 'awaiting_confirmation',
        ])->save();

        $claim->load('donation.category');

        return response()->json([
            'data'    => new ClaimResource($claim),
            'message' => 'OK',
        ]);
    }

    public function verify(Request $request, Claim $claim): JsonResponse
    {
        $claim->load('donation');
        $this->authorize('verify', $claim);

        if (! $claim->proof_uploaded_at || ! $claim->proof_photo_path) {
            return ApiError::respond('INVALID_STATE', 'Bukti pengambilan belum diunggah.', 422);
        }

        $result = DB::transaction(function () use ($claim, $request) {
            $lockedClaim    = Claim::lockForUpdate()->findOrFail($claim->id);
            $lockedDonation = Donation::lockForUpdate()->findOrFail($lockedClaim->donation_id);

            if ($lockedClaim->status !== 'awaiting_confirmation') {
                return ApiError::respond('INVALID_STATE', 'Status klaim tidak valid untuk verifikasi.', 409);
            }

            $lockedClaim->forceFill([
                'verified_at' => now(),
                'verifier_id' => $request->user()->id,
                'status'      => 'completed',
            ])->save();

            $lockedDonation->forceFill(['status' => 'completed'])->save();
        });

        if ($result instanceof JsonResponse) {
            return $result;
        }

        $claim->refresh()->load('donation.category');

        DonationCompleted::dispatch($claim);

        return response()->json([
            'data'    => new ClaimResource($claim),
            'message' => 'OK',
        ]);
    }
}
