<?php

namespace App\Notifications;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DonationCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Donation $donation)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $this->donation->loadMissing('category');

        return [
            'type' => 'donation_cancelled',
            'donation_id' => $this->donation->id,
            'title' => $this->donation->title,
            'category' => $this->donation->category?->name,
            'cancelled_at' => optional($this->donation->updated_at)->toIso8601String(),
        ];
    }
}

