<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClaimProofController extends Controller
{
    public function show(Request $request, Claim $claim): StreamedResponse
    {
        $authUser = $request->user();

        if (! $authUser) {
            abort(401);
        }

        $claim->load('donation');

        $canView = $authUser->isAdmin()
            || $claim->claimer_id === $authUser->id
            || $claim->donation?->donor_id === $authUser->id;

        if (! $canView) {
            abort(403);
        }

        if (! $claim->proof_photo_path) {
            abort(404);
        }

        $disk = Storage::disk('local');

        if (! $disk->exists($claim->proof_photo_path)) {
            abort(404);
        }

        return response()->stream(function () use ($disk, $claim) {
            $stream = $disk->readStream($claim->proof_photo_path);
            if ($stream === false) {
                return;
            }
            fpassthru($stream);
            fclose($stream);
        }, 200, [
            'Content-Type' => 'image/webp',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}

