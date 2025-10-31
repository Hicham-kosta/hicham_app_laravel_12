<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Currency::truncate();
        Currency::create([
            'code' => 'USD', 'symbol' => '$', 'name' => 'US Dollar',
            'rate' => 1.00000000, 'status' => 1, 'is_base' => false, 'flag' => 'us.png'
        ]);
        Currency::create([
            'code' => 'GBP', 'symbol' => '£', 'name' => 'British Pound',
            'rate' => 0.76000000, 'status' => 1, 'is_base' => true, 'flag' => 'gb.png'
        ]);
        Currency::create([
            'code' => 'EUR', 'symbol' => '€', 'name' => 'Euro',
            'rate' => 0.86000000, 'status' => 1, 'is_base' => false, 'flag' => 'eu.png'
        ]);

        Currency::create([
            'code' => 'JPY', 'symbol' => '¥', 'name' => 'Japanese Yen',
            'rate' => 154.00000000, 'status' => 1, 'is_base' => false, 'flag' => 'jp.png'
        ]);
    }
}
