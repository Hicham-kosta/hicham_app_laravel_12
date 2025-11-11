<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;
use Illuminate\Support\Facades\File;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/countries.json');
        if(!File::exists($path)) {
            $this->command->error('Countries data file not found. 
            Create the file at database/seeders/data/countries.json');
            return;
        }
        $json = File::get($path);
        $items = json_decode($json, true);
        foreach ($items as $c) {
            Country::updateOrCreate([
                'name' => $c['name'],
                'code' => $c['code'] ?? null,
                'is_active' => true,
            ]);
        }
        $this->command->info('Countries seeded: ' . count($c));
    }
}
