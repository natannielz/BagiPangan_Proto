<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_categories_index(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'phone' => '081234567890',
            'city' => 'Jakarta',
        ]);

        $response = $this->actingAs($admin)->get('/admin/categories');

        $response->assertOk();
    }

    public function test_admin_can_create_category(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'phone' => '081234567890',
            'city' => 'Jakarta',
        ]);

        $response = $this->actingAs($admin)->post('/admin/categories', [
            'name' => 'Makanan Kering',
            'description' => 'Biskuit, snack.',
            'is_active' => true,
        ]);

        $response->assertRedirect('/admin/categories');
        $this->assertTrue(Category::query()->where('slug', 'makanan-kering')->exists());
    }
}

