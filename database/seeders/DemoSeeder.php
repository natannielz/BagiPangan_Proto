<?php

namespace Database\Seeders;

use App\Models\Claim;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Fixed admin
        User::firstOrCreate(
            ['email' => 'admin@bagipangan.id'],
            [
                'name'              => 'Admin BagiPangan',
                'role'              => 'admin',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Categories
        $this->call(CategorySeeder::class);

        // 3. 10 donors + 10 receivers
        User::factory(10)->create(['role' => 'donor']);
        User::factory(10)->create(['role' => 'receiver']);

        // 4. 50 donations
        Donation::factory(35)->create();           // approved, various statuses
        Donation::factory(10)->pending()->create(); // pending moderation
        Donation::factory(5)->expired()->create();  // expired available

        // 5. 20 claims linked to claimed/completed donations
        Claim::factory(20)->create();
    }
}
