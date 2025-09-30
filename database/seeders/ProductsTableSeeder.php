<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Carbon\Carbon;
use illuminate\Support\Str;


class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menTshirtsCategory = Category::where('name', 'Men T-Shirts')->first();
        if($menTshirtsCategory){
            Product::create([
                'category_id' => $menTshirtsCategory->id,
                'brand_id' => 1,
                'admin_id' => 1,
                'admin_role' => 'admin',
                'product_name' => 'Blue T-Shirt',
                'product_url' => Str::slug('Blue T-Shirt').'-'.uniqid(),
                'product_code' => 'BT001',
                'product_color' => 'Blue',
                'family_color' => 'Blue',
                'group_code' => 'BT000',
                'product_price' => 1000,
                'product_discount' => 10,
                'product_discount_amount' => 100,
                'discount_applied_on' => 'product',
                'product_gst' => 12,
                'final_price' => 900,
                'main_image' => '',
                'product_weight' => 500,
                'product_video' => '',
                'description' => 'test product description',
                'wash_care' => '',
                'search_keywords' => 'blue',
                'fabric' => '',
                'pattern' => '',
                'sleeve' => '',
                'fit' => '',
                'occasion' => '',
                'stock' => 100,
                'sort' => 1,
                'meta_title' => 'Blue T-Shirt',
                'meta_description' => '',
                'meta_keywords' => '',
                'is_featured' => 'No',
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
             ]);
             
             Product::create([
                'category_id' => $menTshirtsCategory->id,
                'brand_id' => 1,
                'admin_id' => 1,
                'admin_role' => 'admin',
                'product_name' => 'Red T-Shirt',
                'product_url' => Str::slug('Red T-Shirt').'-'.uniqid(),
                'product_code' => 'RT001',
                'product_color' => 'Red',
                'family_color' => 'Red',
                'group_code' => 'BT000',
                'product_price' => 2000,
                'product_discount' => 0,
                'product_discount_amount' => 0,
                'discount_applied_on' => '',
                'product_gst' => 12,
                'final_price' => 2000,
                'main_image' => '',
                'product_weight' => 400,
                'product_video' => '',
                'description' => 'test product description',
                'wash_care' => '',
                'search_keywords' => 'blue',
                'fabric' => '',
                'pattern' => '',
                'sleeve' => '',
                'fit' => '',
                'occasion' => '',
                'stock' => 10,
                'sort' => 2,
                'meta_title' => 'Blue T-Shirt',
                'meta_description' => '',
                'meta_keywords' => '',
                'is_featured' => 'Yes',
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
             ]);
         }
    }
}
