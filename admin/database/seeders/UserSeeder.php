<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Using factory create is efficient enough here as password hashing is cached in the factory.
        User::factory()->count(1000)->create();
    }
}
