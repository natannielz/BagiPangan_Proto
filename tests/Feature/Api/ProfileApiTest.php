<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_profile(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/profile');

        $response->assertOk();
        $response->assertJsonPath('data.user.email', $user->email);
    }

    public function test_can_update_profile(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/v1/profile', [
            'name' => 'Nama Baru',
            'email' => 'nama.baru@example.com',
            'phone' => '081234567890',
            'city' => 'Bandung',
            'business_name' => 'Usaha Baru',
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.user.email', 'nama.baru@example.com');
    }

    public function test_can_upload_avatar(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->post('/api/v1/profile/avatar', [
            'avatar' => UploadedFile::fake()->image('avatar.jpg', 1600, 1200)->size(300),
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertOk();
        $this->assertNotNull($user->fresh()->avatar_path);
        Storage::disk('local')->assertExists($user->fresh()->avatar_path);
    }

    public function test_can_get_public_categories(): void
    {
        \App\Models\Category::query()->create([
            'name' => 'Buah',
            'slug' => 'buah',
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/categories');

        $response->assertOk();
        $response->assertJsonPath('data.0.name', 'Buah');
    }
}
