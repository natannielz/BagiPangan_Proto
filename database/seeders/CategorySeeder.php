<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Makanan Siap Saji', 'description' => 'Nasi kotak, lauk siap makan, katering.', 'is_active' => true],
            ['name' => 'Roti & Kue', 'description' => 'Roti, kue, pastry yang masih layak konsumsi.', 'is_active' => true],
            ['name' => 'Buah', 'description' => 'Buah segar dan potongan buah.', 'is_active' => true],
            ['name' => 'Sayur', 'description' => 'Sayuran segar dan olahan sederhana.', 'is_active' => true],
            ['name' => 'Minuman', 'description' => 'Minuman kemasan dan minuman siap saji.', 'is_active' => true],
        ];

        foreach ($items as $item) {
            Category::query()->firstOrCreate(
                ['slug' => Str::slug($item['name'])],
                $item + ['slug' => Str::slug($item['name'])],
            );
        }
    }
}

