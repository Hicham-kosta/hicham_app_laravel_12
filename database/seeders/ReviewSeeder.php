<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Review;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Review::create(['product_id' => 1, 'user_id' => 3, 'rating' => 5, 
        'review' => 'Excellent quality and fast delivery!', 'status' => 1]);
        
        Review::create(['product_id' => 2, 'user_id' => 7, 'rating' => 4, 
        'review' => 'Good product, but the color is not as described.', 'status' => 1]);
    }
}
