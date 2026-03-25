<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class ClaimResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $proofUrl = $this->proof_photo_path
            ? URL::temporarySignedRoute('claims.proof', now()->addHour(), ['claim' => $this->id])
            : null;

        return [
            'id' => $this->id,
            'donation_id' => $this->donation_id,
            'claimer_id' => $this->claimer_id,
            'status' => $this->status,
            'claimed_at' => optional($this->claimed_at)->toIso8601String(),
            'proof_url' => $proofUrl,
            'proof_uploaded_at' => optional($this->proof_uploaded_at)->toIso8601String(),
            'verified_at' => optional($this->verified_at)->toIso8601String(),
            'verifier_id' => $this->verifier_id,
            'donation' => $this->whenLoaded('donation', fn () => new DonationResource($this->donation)),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}

