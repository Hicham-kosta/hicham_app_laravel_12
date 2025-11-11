<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call(AdminsTableSeeder::class);
        $this->call(CategoryTableSeeder::class);
        $this->call(ColorTableSeeder::class);
        $this->call(ProductsAttributesTableSeeder::class);
        $this->call(BrandTableSeeder::class);
        $this->call(ProductsTableSeeder::class);
        $this->call(BannersTableSeeder::class);
        $this->call(FiltersTableSeeder::class);
        $this->call(CouponSeeder::class);
        $this->call(CurrenciesTableSeeder::class);
        $this->call(ReviewSeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(StateSeeder::class);
        
    }
}
