<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            ['name' => 'Electronics'],
            ['name' => 'Clothing'],
            ['name' => 'Home & Kitchen'],
            ['name' => 'Books'],
            ['name' => 'Beauty'],
            ['name' => 'Sports'],
            ['name' => 'Toys'],
            ['name' => 'Automotive'],
            ['name' => 'Groceries'],
            ['name' => 'Health'],
        ]);
    }
}
