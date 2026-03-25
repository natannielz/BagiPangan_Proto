<?php

namespace App\Policies;

use App\Models\Donation;
use App\Models\User;

class DonationPolicy
{
    public function create(User $user): bool
    {
        return $user->isDonor();
    }

    public function update(User $user, Donation $donation): bool
    {
        return $user->isDonor()
            && $donation->donor_id === $user->id
            && $donation->status === 'available';
    }

    public function cancel(User $user, Donation $donation): bool
    {
        return $user->isDonor()
            && $donation->donor_id === $user->id
            && $donation->status === 'available';
    }
}

