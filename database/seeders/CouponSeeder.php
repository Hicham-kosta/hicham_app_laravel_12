<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Coupon;
use Carbon\Carbon;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Coupon::insert([
        [
            'coupon_option' => 'manual',
            'coupon_code' => 'WELCOME10',
            'categories' => null,
            'brands' => null,
            'users' => null,
            'coupon_type' => 'multiple',
            'amount_type' => 'percentage',
            'amount' => 10.00,
            'min_qty' => null,
            'max_qty' => null,
            'min_cart_value' => 0,
            'max_cart_value' => 10000,
            'usage_limit_per_user' => 0,
            'total_usage_limit' => 0,
            'used_count' => 0,
            'max_discount' => null,
            'expiry_date' => Carbon::now()->addMonths(1)->toDateString(),
            'status' => 1,
            'visible' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'coupon_option' => 'manual',
            'coupon_code' => 'FLAT500',
            'categories' => json_encode([1,2,7]),
            'brands' => null,
            'users' => json_encode(['test@example.com']),
            'coupon_type' => 'single',
            'amount_type' => 'fixed',
            'amount' => 200.00,
            'min_qty' => 1,
            'max_qty' => 100,
            'min_cart_value' => 2000.00,
            'max_cart_value' => 5000.00,
            'usage_limit_per_user' => 1,
            'total_usage_limit' => 100,
            'used_count' => 0,
            'max_discount' => null,
            'expiry_date' => Carbon::now()->addMonths(1)->toDateString(),
            'status' => 1,
            'visible' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]
      ]);
    }
}
