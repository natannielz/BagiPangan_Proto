<?php

namespace App\Listeners;

use App\Events\DonationCancelled;
use App\Notifications\DonationCancelledNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Attributes\ListensTo;

#[ListensTo(DonationCancelled::class)]
class SendCancellationNotification implements ShouldQueue
{
    public function handle(DonationCancelled $event): void
    {
        $donation = $event->donation->loadMissing('donor');
        $donor    = $donation->donor;

        if (! $donor) {
            return;
        }

        $donor->notify(new DonationCancelledNotification($donation));
    }
}
