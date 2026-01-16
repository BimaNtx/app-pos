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
        // Create Admin User
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@kasir.app',
            'password' => 'password123',
            'position' => 'admin',
            'is_admin' => true,
            'is_active' => true,
        ]);

        $this->call([
            ProductSeeder::class,
        ]);
    }
}
