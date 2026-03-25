<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AdminSeeder::class);
        $this->call(CategorySeeder::class);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'receiver',
            'phone' => '081234567890',
            'city' => 'Jakarta',
        ]);
    }
}
