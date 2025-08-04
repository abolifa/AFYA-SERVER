<?php

namespace Database\Seeders;

use App\Models\Center;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class NormalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Center::factory(3);
        Product::factory(50)->create();
        Supplier::factory(20)->create();

    }
}
