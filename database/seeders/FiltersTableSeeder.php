<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Filter;
use App\Models\FilterValue;

class FiltersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fabric = Filter::create(['filter_name' => 'fabric', 'sort' => 1, 'status' => 1, 'filter_column' => 'fabric']);
        $sleeve = Filter::create(['filter_name' => 'sleeve', 'sort' => 2, 'status' => 1, 'filter_column' => 'sleeve']);
        FilterValue::insert([
            ['filter_id' => $fabric->id, 'value' => 'Cotton', 'sort' => 1, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['filter_id' => $fabric->id, 'value' => 'Polyester', 'sort' => 2, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['filter_id' => $sleeve->id, 'value' => 'Full sleeve', 'sort' => 1, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['filter_id' => $sleeve->id, 'value' => 'Half sleeve', 'sort' => 2, 'status' => 1, 'created_at' => now(), 'updated_at' => now()]
        ]);
    }
}
