<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CommissionHistory;

class CommissionHistorySeeder extends Seeder
{
    public function run()
    {
        // Create 50 commission records for testing
        CommissionHistory::factory(50)->create();
    }
}