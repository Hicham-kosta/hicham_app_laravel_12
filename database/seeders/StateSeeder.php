<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\State;
use App\Models\Country;
use Illuminate\Support\Facades\File;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/states.json');

        if (!File::exists($path)) {
            $this->command->error('States data file not found. 
            Create it at database/seeders/data/states.json');
            return;
        }

        $json = File::get($path);
        $data = json_decode($json, true);

        $count = 0;

        foreach ($data as $item) {
            $country = Country::where('code', $item['country_id'])->first();

            if (!$country) {
                $this->command->warn("Country not found for code: " . $item['country_id']);
                continue;
            }

            foreach ($item['states'] as $stateName) {
                State::updateOrCreate(
                    [
                        'country_id' => $country->id,
                        'name' => $stateName,
                    ],
                    [
                        'is_active' => true,
                    ]
                );
                $count++;
            }
        }

        $this->command->info("✅ States seeded successfully: {$count}");
    }
}
