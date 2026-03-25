<?php

namespace App\Notifications;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DonationRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Donation $donation) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'donation_id'      => $this->donation->id,
            'title'            => $this->donation->title,
            'rejection_reason' => $this->donation->rejection_reason,
            'message'          => "Donasi \"{$this->donation->title}\" Anda ditolak." .
                ($this->donation->rejection_reason ? " Alasan: {$this->donation->rejection_reason}" : ''),
        ];
    }
}
