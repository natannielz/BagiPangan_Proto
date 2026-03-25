<?php

namespace App\Listeners;

use App\Events\DonationClaimed;
use App\Notifications\DonationClaimedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Attributes\ListensTo;

#[ListensTo(DonationClaimed::class)]
class SendClaimNotification implements ShouldQueue
{
    public function handle(DonationClaimed $event): void
    {
        $claim = $event->claim->loadMissing(['donation.donor', 'claimer']);
        $donor = $claim->donation?->donor;

        if (! $donor) {
            return;
        }

        $donor->notify(new DonationClaimedNotification($claim));
    }
}
