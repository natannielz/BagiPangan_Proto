<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AuthTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_login_redirects_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@test.com', 'password' => bcrypt('password')]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->visit('/login')
                ->type('email', $admin->email)
                ->type('password', 'password')
                ->press('Log in')
                ->assertPathIs('/admin/dashboard');
        });
    }

    public function test_login_redirects_donor(): void
    {
        $donor = User::factory()->create(['role' => 'donor', 'password' => bcrypt('password')]);

        $this->browse(function (Browser $browser) use ($donor) {
            $browser->visit('/login')
                ->type('email', $donor->email)
                ->type('password', 'password')
                ->press('Log in')
                ->assertPathIs('/donor/dashboard');
        });
    }

    public function test_login_redirects_receiver(): void
    {
        $receiver = User::factory()->create(['role' => 'receiver', 'password' => bcrypt('password')]);

        $this->browse(function (Browser $browser) use ($receiver) {
            $browser->visit('/login')
                ->type('email', $receiver->email)
                ->type('password', 'password')
                ->press('Log in')
                ->assertPathIs('/donations');
        });
    }

    public function test_suspended_cannot_login(): void
    {
        $user = User::factory()->create([
            'role'         => 'donor',
            'password'     => bcrypt('password'),
            'suspended_at' => now()->subDay(),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                ->type('email', $user->email)
                ->type('password', 'password')
                ->press('Log in')
                ->assertSee('ditangguhkan');
        });
    }

    public function test_register_step_one_select_role(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->assertSee('Donatur')
                ->click('@role-donor')
                ->assertVisible('#step2');
        });
    }

    public function test_password_show_hide(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertAttribute('#password', 'type', 'password')
                ->click('button[class*="absolute"]')
                ->assertAttribute('#password', 'type', 'text');
        });
    }

    public function test_email_uniqueness_check(): void
    {
        $existing = User::factory()->create(['email' => 'taken@example.com']);

        $this->browse(function (Browser $browser) use ($existing) {
            $browser->visit('/register')
                ->click('@role-donor')
                ->waitFor('#step2')
                ->type('email', $existing->email)
                ->click('#name') // blur away from email field
                ->waitForText('sudah terdaftar')
                ->assertSee('sudah terdaftar');
        });
    }
}
