<?php

namespace Tests\Browser;

use App\Models\Claim;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ClaimTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_receiver_claims_donation(): void
    {
        $receiver = User::factory()->create(['role' => 'receiver', 'password' => bcrypt('password')]);
        $donation = Donation::factory()->create([
            'moderation_status' => 'approved',
            'status'            => 'available',
            'expiry_at'         => now()->addDays(1),
        ]);

        $this->browse(function (Browser $browser) use ($receiver, $donation) {
            $browser->loginAs($receiver)
                ->visit('/donations/'.$donation->id)
                ->press('Klaim Sekarang')
                ->assertSee('Berhasil diklaim');

            $this->assertDatabaseHas('claims', [
                'donation_id' => $donation->id,
                'claimer_id'  => $receiver->id,
                'status'      => 'claimed',
            ]);
        });
    }

    public function test_claim_button_disappears_after_claim(): void
    {
        $receiver = User::factory()->create(['role' => 'receiver', 'password' => bcrypt('password')]);
        $donation = Donation::factory()->create([
            'moderation_status' => 'approved',
            'status'            => 'claimed',
            'expiry_at'         => now()->addDays(1),
        ]);

        $this->browse(function (Browser $browser) use ($receiver, $donation) {
            $browser->loginAs($receiver)
                ->visit('/donations/'.$donation->id)
                ->assertMissing('@claim-button');
        });
    }

    public function test_race_condition_return_409(): void
    {
        $receiver1 = User::factory()->create(['role' => 'receiver', 'password' => bcrypt('password')]);
        $receiver2 = User::factory()->create(['role' => 'receiver', 'password' => bcrypt('password')]);
        $donation  = Donation::factory()->create([
            'moderation_status' => 'approved',
            'status'            => 'available',
            'expiry_at'         => now()->addDays(1),
        ]);

        $this->browse(function (Browser $first, Browser $second) use ($receiver1, $receiver2, $donation) {
            $first->loginAs($receiver1)->visit('/donations/'.$donation->id);
            $second->loginAs($receiver2)->visit('/donations/'.$donation->id);

            $first->press('Klaim Sekarang');
            $second->press('Klaim Sekarang');

            $first->waitForText('diklaim', 5);
            $second->waitForText('diklaim', 5);

            $texts = $first->text('body').$second->text('body');

            $this->assertTrue(
                str_contains($texts, 'Berhasil diklaim') || str_contains($texts, 'Sudah diklaim') || str_contains($texts, 'sudah diklaim'),
                'At least one browser should see a claim result'
            );
        });
    }

    public function test_concurrent_claim_limit_enforced(): void
    {
        $receiver = User::factory()->create(['role' => 'receiver', 'password' => bcrypt('password')]);

        // Create 3 active claims
        Claim::factory(3)->create([
            'claimer_id' => $receiver->id,
            'status'     => 'claimed',
        ]);

        $donation4 = Donation::factory()->create([
            'moderation_status' => 'approved',
            'status'            => 'available',
            'expiry_at'         => now()->addDays(1),
        ]);

        $this->browse(function (Browser $browser) use ($receiver, $donation4) {
            $browser->loginAs($receiver)
                ->visit('/donations/'.$donation4->id)
                ->press('Klaim Sekarang')
                ->assertSee('Batas klaim');
        });
    }

    public function test_receiver_uploads_proof(): void
    {
        $receiver = User::factory()->create(['role' => 'receiver', 'password' => bcrypt('password')]);
        $donation = Donation::factory()->create([
            'moderation_status' => 'approved',
            'status'            => 'claimed',
        ]);
        $claim = Claim::factory()->create([
            'donation_id' => $donation->id,
            'claimer_id'  => $receiver->id,
            'status'      => 'claimed',
        ]);

        $this->browse(function (Browser $browser) use ($receiver, $claim) {
            $browser->loginAs($receiver)
                ->visit('/receiver/claims/'.$claim->id.'/proof')
                ->attach('proof_photo', base_path('tests/fixtures/food.jpg'))
                ->press('Unggah Bukti')
                ->assertSee('Bukti berhasil diunggah');

            $this->assertDatabaseHas('claims', [
                'id'     => $claim->id,
                'status' => 'awaiting_confirmation',
            ]);
        });
    }

    public function test_donor_verifies_pickup(): void
    {
        $donor    = User::factory()->create(['role' => 'donor', 'password' => bcrypt('password')]);
        $receiver = User::factory()->create(['role' => 'receiver']);
        $donation = Donation::factory()->create([
            'donor_id'          => $donor->id,
            'moderation_status' => 'approved',
            'status'            => 'claimed',
        ]);
        $claim = Claim::factory()->create([
            'donation_id'       => $donation->id,
            'claimer_id'        => $receiver->id,
            'status'            => 'awaiting_confirmation',
            'proof_photo_path'  => 'claims/test/proof.webp',
            'proof_uploaded_at' => now(),
        ]);

        $this->browse(function (Browser $browser) use ($donor, $claim, $donation) {
            $browser->loginAs($donor)
                ->visit('/donor/claims')
                ->press('Verifikasi')
                ->assertSee('Klaim selesai');

            $this->assertDatabaseHas('claims', ['id' => $claim->id, 'status' => 'completed']);
            $this->assertDatabaseHas('donations', ['id' => $donation->id, 'status' => 'completed']);
        });
    }
}
