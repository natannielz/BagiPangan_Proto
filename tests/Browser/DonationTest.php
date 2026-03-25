<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DonationTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_donor_creates_donation(): void
    {
        $donor = User::factory()->create(['role' => 'donor', 'password' => bcrypt('password')]);
        Category::factory()->create(['name' => 'Makanan Siap Saji', 'slug' => 'makanan-siap-saji', 'is_active' => true]);

        $title = 'Nasi Kotak Browser Test ' . now()->timestamp;

        $this->browse(function (Browser $browser) use ($donor, $title) {
            $browser->loginAs($donor)
                ->visit('/donor/donations/create')
                ->type('title', $title)
                ->type('description', 'Deskripsi test donasi yang cukup panjang untuk validasi.')
                ->type('qty_portions', '5')
                ->type('location_district', 'Jakarta Pusat')
                ->type('expiry_at', now()->addDays(2)->format('Y-m-d\TH:i'))
                ->attach('photo', base_path('tests/fixtures/food.jpg'))
                ->press('Posting Donasi')
                ->assertSee('berhasil');

            $this->assertDatabaseHas('donations', [
                'title'             => $title,
                'moderation_status' => 'pending',
            ]);
        });
    }

    public function test_donation_not_visible_before_approval(): void
    {
        $donation = Donation::factory()->create([
            'title'             => 'Donasi Pending Test',
            'moderation_status' => 'pending',
            'status'            => 'available',
            'expiry_at'         => now()->addDays(1),
        ]);

        $this->browse(function (Browser $browser) use ($donation) {
            $browser->visit('/donations')
                ->assertDontSee($donation->title);
        });
    }

    public function test_admin_approves_makes_donation_visible(): void
    {
        $donor  = User::factory()->create(['role' => 'donor']);
        $admin  = User::factory()->create(['role' => 'admin', 'password' => bcrypt('password')]);
        $title  = 'Donasi Untuk Disetujui ' . now()->timestamp;

        $donation = Donation::factory()->create([
            'donor_id'          => $donor->id,
            'title'             => $title,
            'moderation_status' => 'pending',
            'status'            => 'available',
            'expiry_at'         => now()->addDays(2),
        ]);

        $this->browse(function (Browser $browser) use ($admin, $donor, $donation, $title) {
            $browser->loginAs($admin)
                ->visit('/admin/donations')
                ->press('Setujui');

            $browser->loginAs($donor)
                ->visit('/donations')
                ->assertSee($title);
        });
    }

    public function test_expiry_shows_red_when_near(): void
    {
        Donation::factory()->create([
            'title'             => 'Donasi Hampir Kadaluarsa',
            'moderation_status' => 'approved',
            'status'            => 'available',
            'expiry_at'         => now()->addMinutes(90),
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/donations')
                ->assertPresent('.expiry-badge.text-red-600');
        });
    }
}
