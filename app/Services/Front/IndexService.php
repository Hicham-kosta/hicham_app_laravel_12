<?php

namespace App\Services\Front;

use App\Models\Banner;
use App\Models\Product;
use App\Models\Category;

class IndexService
{
    /**
     * Get the list of banners.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getHomePageBanners()
    {
        $homeSliderBanners = Banner::where('type', 'Slider')
            ->where('status', 1)
            ->orderBy('sort', 'DESC')
            ->get()
            ->toArray();

            $homeFixedBanners = Banner::where('type', 'Fix')
            ->where('status', 1)
            ->orderBy('sort', 'DESC')
            ->get()
            ->toArray();

            $logoBanners = Banner::where('type', 'Logo') // logo banners for brands logos
            ->where('status', 1)
            ->orderBy('sort', 'DESC')
            ->get()
            ->toArray();

            return compact('homeSliderBanners', 'homeFixedBanners', 'logoBanners');
    }

    public function featuredProducts()
    {
        $featuredProducts = Product::select('id', 'category_id', 'product_name', 'discount_applied_on', 
        'product_price', 'product_discount', 'final_price', 'group_code', 'main_image')
            ->with(['product_images'])
            ->where(['is_featured' => 'Yes', 'status' => 1])
            ->where('stock' , '>', 0)
            ->inRandomOrder()
            ->limit(8)
            ->get()
            ->toArray();
        return compact('featuredProducts');
    }

    public function newArrivalsProducts(){
        $newArrivalsProducts = Product::select('id', 'category_id', 'product_name', 'discount_applied_on', 
        'product_price', 'product_discount', 'final_price', 'group_code', 'main_image')
            ->with(['product_images'])
            ->where('status', 1)
            ->where('stock' , '>', 0)
            ->latest()
            ->orderBy('id', 'DESC')
            ->limit(8)
            ->get()
            ->toArray();
        return compact('newArrivalsProducts');
    }

    public function homecategories(){
        $categories = Category::select('id', 'name', 'image', 'url')
            ->whereNull('parent_id') // Only fetch top-level (parent) categories
            ->where('status', 1) // Only fetch active categories
            ->where('menu_status', 1) // Only fetch categories that should be displayed in the menu
            ->get()
            ->map(function ($category) {
                $allCategoryIds = $this->getAllCategoryIds($category->id); // Get this category and all its subcategories
                $productCount = Product::whereIn('category_id', $allCategoryIds)
                    ->where('status', 1)
                    ->where('stock', '>', 0)
                    ->count(); // Count Active + in-stock products across all levels
                    
                return  [
                    'id' => $category->id,
                    'name' => $category->name,
                    'image' => $category->image,
                    'url' => $category->url,
                    'product_count' => $productCount // Attach product count to the category
                ];   
            });
            return ['categories' => $categories->toArray()];
    }

    private function getAllCategoryIds($parentId)
    {
        $categoryIds = [$parentId]; // Start with the parent category ID
        $childIds = Category::where('parent_id', $parentId)
            ->where('status', 1) // Only fetch active child categories
            ->pluck('id'); // Get all child category IDs
        foreach ($childIds as $childId) {
            $categoryIds = array_merge($categoryIds, $this->getAllCategoryIds($childId)); // Recursively get all subcategory IDs
        }
        return $categoryIds; // Return all child + sub child category IDs
    }
}