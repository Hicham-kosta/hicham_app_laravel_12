<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Banner;

class BannersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bannerRecords = [
            ['id'=>1, 'image'=>'carousel-1.jpg', 'type'=>'Slider', 'link'=>'', 'title'=>'Products on Sale', 
            'alt'=>'Products on Sale', 'sort'=>1, 'status'=>1],
            ['id'=>2, 'image'=>'carousel-2.jpg', 'type'=>'Slider', 'link'=>'', 'title'=>'Flat 50% Off', 
            'alt'=>'Flat 50% Off', 'sort'=>2, 'status'=>1],
            ['id'=>3, 'image'=>'carousel-3.jpg', 'type'=>'Slider', 'link'=>'#', 'title'=>'Summer Sale', 
            'alt'=>'Summer Sale', 'sort'=>3, 'status'=>1],
        ];

        foreach($bannerRecords as $record){
            Banner::create($record); // Auto fills created_at and updated_at timestamps
        }
        
    }
}
