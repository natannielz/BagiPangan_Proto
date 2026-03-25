<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Donation>
 */
class DonationFactory extends Factory
{
    public function definition(): array
    {
        $status = fake()->randomElement([
            'available', 'available', 'available', 'claimed', 'completed',
        ]);

        return [
            'donor_id'          => User::factory()->state(['role' => 'donor']),
            'category_id'       => Category::inRandomOrder()->value('id'),
            'title'             => fake()->randomElement([
                'Nasi Kotak Sisa Acara', 'Lauk Pauk Rumahan', 'Buah Segar',
                'Roti Gandum', 'Sayur Mayur', 'Kue Brownies', 'Ayam Goreng', 'Mie Goreng',
            ]).' - '.fake()->city(),
            'description'       => fake()->sentence(12),
            'qty_portions'      => fake()->numberBetween(1, 20),
            'location_district' => fake()->city(),
            'expiry_at'         => fake()->dateTimeBetween('+2 hours', '+5 days'),
            'photo_path'        => 'donations/placeholder.webp',
            'status'            => $status,
            'moderation_status' => 'approved',
        ];
    }

    public function pending(): static
    {
        return $this->state(['moderation_status' => 'pending']);
    }

    public function expired(): static
    {
        return $this->state([
            'expiry_at' => now()->subHours(2),
            'status'    => 'available',
        ]);
    }
}
