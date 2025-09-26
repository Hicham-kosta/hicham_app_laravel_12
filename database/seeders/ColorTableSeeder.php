<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Color;

class ColorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $colors = ['Black', 'Blue', 'Brown', 'Green', 'Grey', 'Multi', 'Olive', 'Orange', 'Pink', 'Purple',
         'Red', 'White', 'Yellow'];
        foreach ($colors as $colorName) {
            $color = new Color;
            $color->name = $colorName;
            $color->status = 1; // Assuming 1 means active
            $color->save();          
    }
 }
}
