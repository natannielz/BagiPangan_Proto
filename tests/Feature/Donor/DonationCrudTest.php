<?php

namespace Tests\Feature\Donor;

use App\Models\Category;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_donor_can_create_donation(): void
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

        $response = $this->actingAs($donor)->post('/donor/donations', [
            'category_id' => $category->id,
            'title' => 'Roti',
            'description' => 'Roti tawar',
            'qty_portions' => 5,
            'location_district' => 'Cilandak',
            'expiry_at' => now()->addDay()->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect('/donor/donations');
        $this->assertTrue(Donation::query()->where('donor_id', $donor->id)->where('title', 'Roti')->exists());
    }

    public function test_receiver_cannot_access_donor_donations_page(): void
    {
        $receiver = User::factory()->create([
            'role' => 'receiver',
            'phone' => '081234567890',
            'city' => 'Jakarta',
        ]);

        $response = $this->actingAs($receiver)->get('/donor/donations');

        $response->assertStatus(403);
    }
}

