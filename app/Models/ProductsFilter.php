<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\ProductsAttribute;
use App\Models\Brand;

class ProductsFilter extends Model
{
    public static function getColors($catIds){
        $getProductIds = Product::select('id')->whereIn('category_id', $catIds)
        ->pluck('id')->toArray();
        if(empty($getProductIds)){
            return [];
        }

        $getProductColors = Product::select('family_color')
        ->whereIn('id', $getProductIds)
        ->whereNotNull('family_color')
        ->groupBy('family_color')
        ->pluck('family_color')
        ->toArray();
        
        return $getProductColors ?? [];
    }

    public static function getSizes($catIds){
        $getProductIds = Product::select('id')
        ->whereIn('category_id', $catIds)
        ->pluck('id')->toArray();
        if(empty($getProductIds)){
            return [];
        }

        $getProductSizes = ProductsAttribute::select('size')
        ->where('status', 1)
        ->whereIn('product_id', $getProductIds)
        ->groupBy('size')
        ->pluck('size')
        ->toArray();

        return $getProductSizes ?? [];

    }

    public static function getBrands($catIds){
        $getProductIds = Product::select('id')
        ->whereIn('category_id', $catIds)
        ->pluck('id')
        ->toArray();
        if(empty($getProductIds)){
            return [];
        }

        $getProductBrandIds = Product::select('brand_id')
        ->whereIn('id', $getProductIds)
        ->whereNotNull('brand_id')
        ->groupBy('brand_id')
        ->pluck('brand_id')
        ->toArray();

        $getProductBrands = Brand::select('id', 'name')
        ->where('status', 1)
        ->whereIn('id', $getProductBrandIds)
        ->orderBy('name', 'ASC')
        ->get()
        ->toArray();

        return $getProductBrands ?? [];
    }
}
