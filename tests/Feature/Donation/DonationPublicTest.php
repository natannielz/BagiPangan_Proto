<?php

namespace Tests\Feature\Donation;

use App\Models\Category;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationPublicTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_donations_index(): void
    {
        $response = $this->get('/donations');

        $response->assertOk();
    }

    public function test_guest_can_view_donation_detail(): void
    {
        $donor = User::factory()->create([
            'role' => 'donor',
            'phone' => '081234567890',
            'city' => 'Jakarta',
        ]);

        $category = Category::query()->create([
            'name' => 'Makanan',
            'slug' => 'makanan',
            'is_active' => true,
        ]);

        $donation = Donation::query()->create([
            'donor_id' => $donor->id,
            'category_id' => $category->id,
            'title' => 'Nasi kotak',
            'qty_portions' => 10,
            'location_district' => 'Kebayoran',
            'expiry_at' => now()->addDay(),
            'status' => 'available',
            'moderation_status' => 'approved',
        ]);

        $response = $this->get('/donations/'.$donation->id);

        $response->assertOk();
    }
}
