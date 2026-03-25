<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class DonationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $photoUrl = $this->photo_path
            ? URL::temporarySignedRoute('donations.photo', now()->addHour(), ['donation' => $this->id])
            : null;

        return [
            'id' => $this->id,
            'donor_id' => $this->donor_id,
            'category' => $this->whenLoaded('category', fn () => new CategoryResource($this->category)),
            'title' => $this->title,
            'description' => $this->description,
            'qty_portions' => $this->qty_portions,
            'location_district' => $this->location_district,
            'expiry_at' => optional($this->expiry_at)->toIso8601String(),
            'photo_url' => $photoUrl,
            'status'            => $this->status,
            'moderation_status' => $this->moderation_status,
            'rejection_reason'  => $this->when(
                $this->moderation_status === 'rejected',
                $this->rejection_reason
            ),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}

