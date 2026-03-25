<?php

namespace Tests\Browser;

use App\Models\Claim;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_admin_rejects_donation_with_reason(): void
    {
        $admin  = User::factory()->create(['role' => 'admin', 'password' => bcrypt('password')]);
        $donor  = User::factory()->create(['role' => 'donor']);
        $donation = Donation::factory()->create([
            'donor_id'          => $donor->id,
            'moderation_status' => 'pending',
            'status'            => 'available',
        ]);

        $this->browse(function (Browser $browser) use ($admin, $donation) {
            $browser->loginAs($admin)
                ->visit('/admin/donations')
                ->press('Tolak')
                ->waitFor('.fixed.inset-0', 3)
                ->type('textarea', 'Foto tidak jelas dan deskripsi kurang lengkap.')
                ->press('Ya, Tolak')
                ->assertSee('berhasil ditolak');

            $this->assertDatabaseHas('donations', [
                'id'                => $donation->id,
                'moderation_status' => 'rejected',
            ]);
        });
    }

    public function test_admin_cannot_reject_claimed_donation(): void
    {
        $admin  = User::factory()->create(['role' => 'admin', 'password' => bcrypt('password')]);
        $donor  = User::factory()->create(['role' => 'donor']);
        Donation::factory()->create([
            'donor_id'          => $donor->id,
            'moderation_status' => 'pending',
            'status'            => 'claimed',
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/donations')
                ->assertMissing('Tolak');
        });
    }

    public function test_csv_download(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'password' => bcrypt('password')]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/reports')
                ->assertSee('Unduh CSV');
        });
    }

    public function test_dashboard_charts_render(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'password' => bcrypt('password')]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/dashboard')
                ->waitFor('canvas', 5)
                ->assertPresent('canvas')
                ->assertScript('return typeof lineChart !== "undefined"');
        });
    }
}
