<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_public_donations(): void
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

        Donation::query()->create([
            'donor_id' => $donor->id,
            'category_id' => $category->id,
            'title' => 'Nasi',
            'qty_portions' => 1,
            'location_district' => 'Kemang',
            'expiry_at' => now()->addDay(),
            'status' => 'available',
            'moderation_status' => 'approved',
        ]);

        $response = $this->getJson('/api/v1/donations');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'qty_portions', 'location_district', 'expiry_at', 'status'],
            ],
            'message',
        ]);
    }
}
