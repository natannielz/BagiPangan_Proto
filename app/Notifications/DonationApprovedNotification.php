<?php

namespace App\Notifications;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DonationApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Donation $donation) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'donation_id' => $this->donation->id,
            'title'       => $this->donation->title,
            'message'     => "Donasi \"{$this->donation->title}\" Anda telah disetujui dan sekarang tampil di listing publik.",
        ];
    }
}
