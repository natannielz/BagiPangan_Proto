<?php

namespace App\Notifications;

use App\Models\Claim;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DonationCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Claim $claim)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $this->claim->loadMissing(['donation', 'claimer', 'verifier']);

        return [
            'type' => 'donation_completed',
            'donation_id' => $this->claim->donation_id,
            'claim_id' => $this->claim->id,
            'title' => $this->claim->donation?->title,
            'claimer_name' => $this->claim->claimer?->name,
            'verifier_name' => $this->claim->verifier?->name,
            'verified_at' => optional($this->claim->verified_at)->toIso8601String(),
        ];
    }
}

