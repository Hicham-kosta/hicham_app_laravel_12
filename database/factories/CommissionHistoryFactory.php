<?php

namespace Database\Factories;

use App\Models\CommissionHistory;
use App\Models\Order;
use App\Models\Admin;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommissionHistoryFactory extends Factory
{
    protected $model = CommissionHistory::class;

    public function definition()
    {
        $subtotal = $this->faker->randomFloat(2, 100, 10000);
        $commissionPercent = $this->faker->randomFloat(2, 5, 30);
        $commissionAmount = ($subtotal * $commissionPercent) / 100;
        $vendorAmount = $subtotal - $commissionAmount;
        
        return [
            'order_id' => Order::factory(),
            'vendor_id' => Admin::factory()->state(['role' => 'vendor']),
            'product_id' => Product::factory(),
            'product_name' => $this->faker->words(3, true),
            'sku' => $this->faker->unique()->bothify('SKU-#####'),
            'size' => $this->faker->randomElement(['S', 'M', 'L', 'XL', 'XXL']),
            'item_price' => $this->faker->randomFloat(2, 10, 1000),
            'quantity' => $this->faker->numberBetween(1, 10),
            'subtotal' => $subtotal,
            'commission_percent' => $commissionPercent,
            'commission_amount' => $commissionAmount,
            'vendor_amount' => $vendorAmount,
            'gst_percent' => $this->faker->randomFloat(2, 0, 28),
            'gst_amount' => $this->faker->randomFloat(2, 0, 500),
            'status' => $this->faker->randomElement(['pending', 'paid', 'cancelled']),
            'payment_date' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
            'payment_method' => $this->faker->optional()->randomElement(['bank_transfer', 'paypal', 'stripe', 'cash']),
            'payment_reference' => $this->faker->optional()->bothify('REF-########'),
            'payment_notes' => $this->faker->optional()->sentence(),
            'commission_date' => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'month' => $this->faker->numberBetween(1, 12),
            'year' => $this->faker->numberBetween(2023, 2025),
        ];
    }
}