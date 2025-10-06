<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        Category::create(['type' => 'income', 'name' => 'Gaji']);
        Category::create(['type' => 'income', 'name' => 'Bonus']);
        Category::create(['type' => 'expense', 'name' => 'Makanan']);
        Category::create(['type' => 'expense', 'name' => 'Transportasi']);
        Category::create(['type' => 'expense', 'name' => 'Hiburan']);
    }
}
