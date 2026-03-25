<?php

namespace App\Policies;

use App\Models\Claim;
use App\Models\User;

class ClaimPolicy
{
    public function create(User $user): bool
    {
        return $user->isReceiver();
    }

    public function uploadProof(User $user, Claim $claim): bool
    {
        return $user->isReceiver()
            && $claim->claimer_id === $user->id
            && in_array($claim->status, ['claimed', 'awaiting_confirmation'], true);
    }

    public function verify(User $user, Claim $claim): bool
    {
        if ($user->isAdmin()) {
            return $claim->status === 'awaiting_confirmation';
        }

        return $user->isDonor()
            && $claim->donation?->donor_id === $user->id
            && $claim->status === 'awaiting_confirmation';
    }
}

