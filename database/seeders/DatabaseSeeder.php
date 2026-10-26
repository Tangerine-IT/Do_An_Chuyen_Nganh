<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database. Order matters: later seeders look up
     * rows created by earlier ones.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            ProductSeeder::class,
            ServiceSeeder::class,
            CouponSeeder::class,
            PostSeeder::class,
            AdoptionListingSeeder::class,
            LocationSeeder::class,
            ReviewSeeder::class,
        ]);
    }
}
