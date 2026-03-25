<?php

namespace Tests\Feature\Receiver;

use App\Models\Category;
use App\Models\Claim;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClaimFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_receiver_can_claim_upload_proof_and_donor_can_verify(): void
    {
        Storage::fake('local');

        $donor = User::factory()->create([
            'role' => 'donor',
            'phone' => '081234567890',
            'city' => 'Jakarta',
        ]);

        $receiver = User::factory()->create([
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

        $claimResponse = $this->actingAs($receiver)->post('/receiver/donations/'.$donation->id.'/claim');
        $claimResponse->assertRedirect('/receiver/claims');

        $this->assertSame('claimed', $donation->fresh()->status);
        $this->assertSame(1, DatabaseNotification::query()->where('notifiable_id', $donor->id)->count());

        $claim = Claim::query()->where('donation_id', $donation->id)->firstOrFail();
        $this->assertSame('claimed', $claim->status);

        $proofResponse = $this->actingAs($receiver)->post('/receiver/claims/'.$claim->id.'/proof', [
            'proof_photo' => UploadedFile::fake()->image('proof.jpg', 1200, 900)->size(300),
        ]);
        $proofResponse->assertRedirect('/receiver/claims');

        $claim = $claim->fresh();
        $this->assertSame('awaiting_confirmation', $claim->status);
        $this->assertNotNull($claim->proof_photo_path);
        Storage::disk('local')->assertExists($claim->proof_photo_path);

        $verifyResponse = $this->actingAs($donor)->post('/donor/claims/'.$claim->id.'/verify');
        $verifyResponse->assertRedirect('/donor/claims');

        $this->assertSame('completed', $donation->fresh()->status);
        $this->assertSame('completed', $claim->fresh()->status);
        $this->assertSame(2, DatabaseNotification::query()->where('notifiable_id', $donor->id)->count());
        $this->assertSame(1, DatabaseNotification::query()->where('notifiable_id', $receiver->id)->count());
    }
}
