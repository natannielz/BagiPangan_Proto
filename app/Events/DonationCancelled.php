<?php

namespace App\Events;

use App\Models\Donation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DonationCancelled
{
    use Dispatchable, SerializesModels;

    public function __construct(public Donation $donation)
    {
    }
}

