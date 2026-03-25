<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@bagipangan.test');

        User::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => env('ADMIN_NAME', 'Admin BagiPangan'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
                'role' => 'admin',
                'phone' => env('ADMIN_PHONE', '081234567890'),
                'city' => env('ADMIN_CITY', 'Jakarta'),
            ],
        );
    }
}
