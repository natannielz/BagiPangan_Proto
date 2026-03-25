<?php

namespace App\Listeners;

use App\Events\DonationCompleted;
use App\Notifications\DonationCompletedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Attributes\ListensTo;

#[ListensTo(DonationCompleted::class)]
class SendCompletionNotification implements ShouldQueue
{
    public function handle(DonationCompleted $event): void
    {
        $claim   = $event->claim->loadMissing(['donation.donor', 'claimer', 'verifier']);
        $donor   = $claim->donation?->donor;
        $claimer = $claim->claimer;

        if ($donor) {
            $donor->notify(new DonationCompletedNotification($claim));
        }

        if ($claimer) {
            $claimer->notify(new DonationCompletedNotification($claim));
        }
    }
}
