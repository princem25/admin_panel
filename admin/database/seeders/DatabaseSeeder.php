<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
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
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('admin@123'),
                'role' => 'admin',
            ]
        );

        if (Category::count() === 0) {
            $this->call(CategorySeeder::class);
        }

        $this->call([
            UserSeeder::class,
            ProductSeeder::class,
        ]);
    }
}