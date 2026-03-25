<?php

namespace Database\Factories;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Claim>
 */
class ClaimFactory extends Factory
{
    public function definition(): array
    {
        $donation = Donation::where('status', 'claimed')->inRandomOrder()->first()
            ?? Donation::factory()->state(['status' => 'claimed'])->create();

        return [
            'donation_id'      => $donation->id,
            'claimer_id'       => User::factory()->state(['role' => 'receiver']),
            'claimed_at'       => now()->subHours(fake()->numberBetween(1, 48)),
            'status'           => fake()->randomElement(['claimed', 'awaiting_confirmation', 'completed']),
            'proof_photo_path' => null,
            'verified_at'      => null,
        ];
    }
}
