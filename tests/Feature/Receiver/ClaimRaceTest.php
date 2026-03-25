<?php

namespace Tests\Feature\Receiver;

use App\Models\Category;
use App\Models\Claim;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClaimRaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_second_claim_on_same_donation_gets_conflict(): void
    {
        $donor = User::factory()->create([
            'role' => 'donor',
            'phone' => '081234567890',
            'city' => 'Jakarta',
        ]);

        $receiver1 = User::factory()->create([
            'role' => 'receiver',
            'phone' => '081234567890',
            'city' => 'Jakarta',
        ]);

        $receiver2 = User::factory()->create([
            'role' => 'receiver',
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

        $this->actingAs($receiver1)->post('/receiver/donations/'.$donation->id.'/claim')->assertRedirect('/receiver/claims');

        $this->actingAs($receiver2)->post('/receiver/donations/'.$donation->id.'/claim')->assertStatus(409);

        $this->assertSame('claimed', $donation->fresh()->status);
        $this->assertSame(1, Claim::query()->where('donation_id', $donation->id)->count());
    }
}

