<?php

namespace App\Services\Front;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductsAttribute;
use App\Models\Brand;
use App\Models\Filter;
use Illuminate\Support\Facades\View;

class ProductService{

    public function getCategoryListingDataOld($url){

        $categoryInfo = Category::categoryDetails($url);

        $query = Product::with(['product_images'])
            ->whereIn('category_id', $categoryInfo['catIds'])
            ->where('status', 1);

        // Apply filters(sort)
        $query = $this->applyFilters($query);
        $products = $query->paginate(0)->withQueryString();

        // Fetch filters with values
        $filters = Filter::with(['values' => function($q){
            $q->where('status', 1)->orderBy('sort', 'asc');
        }])->where('status', 1)->orderBy('sort', 'asc')->get();

        // If Ajax call (for filters)
        /*if(request()->has('json')){
            $view = View::make('front.products.ajax_products_listing', [
                'categoryDetails' => $categoryInfo['categoryDetails'],
                'categoryProducts' => $products,
                'breadcrumbs' => $categoryInfo['breadcrumbs'],
                'selectedSort' => request()->get('sort', ''),
                'url' => $url,
            ])->render();
            return response()->json(['view' => $view]);
        }
        */

        return [
            'categoryDetails' => $categoryInfo['categoryDetails'],
            'categoryProducts' => $products,
            'breadcrumbs' => $categoryInfo['breadcrumbs'],
            'selectedSort' => request()->get('sort', 'product_latest'),
            'url' => $url,
            'catIds' => $categoryInfo['catIds'],
            'filters' => $filters
        ];
    }

    public function getCategoryListingData($url){
        $categoryInfo = Category::categoryDetails($url);
        $catIds = $categoryInfo['catIds']; // all category + subcategory ids

        $query = Product::with(['product_images'])
          ->where('status', 1)
          ->where(function ($q) use($catIds){
            // Condition 1: product's main category
            $q->orWhereIn('category_id', $catIds)
            // Condition 2: product assigned via pivot products_categories
            ->orWhereHas('categories', function ($subQ) use($catIds){
                $subQ->whereIn('categories.id', $catIds);
            });
          });

          // Apply filters (color, size, price, brand, dynamic filters etc...)
          $query = $this->applyFilters($query);
          $products = $query->paginate(8)->withQueryString();

          // Fetch filters with values
          $filters = Filter::with(['values' => function($q){
             $q->where('status', 1)->orderBy('sort', 'asc');
          }])->where('status', 1)->orderBy('sort', 'asc')->get();

          return [
            'categoryDetails' => $categoryInfo['categoryDetails'],
            'categoryProducts' => $products,
            'breadcrumbs' => $categoryInfo['breadcrumbs'],
            'selectedSort' => request()->get('sort', 'product_latest'),
            'url' => $url,
            'catIds' => $catIds,
            'filters' => $filters
          ];
    }

    private function applyFilters($query)
    {
        //Apply Sorting logic
        $sort = request()->get('sort');

        switch ($sort) {
            case 'product_latest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'lowest_price':
                $query->orderBy('final_price', 'asc');
                break;
            case 'highest_price':
                $query->orderBy('final_price', 'desc');
                break;
            case 'best_selling':
                $query->inRandomOrder('final_price', 'desc'); //Temporary until sales data is available
                break;
            case 'featured_items':
                $query->where('is_featured', 'Yes')
                      ->orderBy('created_at', 'desc');
                break;
            case 'discounted_items':
                $query->where('product_discount', '>', 0);
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        //Apply Color Filter
        if(request()->has('color') && !empty(request()->get('color'))){
            $colors = array_filter(explode('~', request()->get('color')));
            if(count($colors) > 0){
                $query->whereIn('family_color', $colors);
            }
        }

        // Apply Size Filter
        if(request()->has('size') && !empty(request()->get('size'))){
            $sizes = explode('~', request()->get('size'));
            $getProductIds = ProductsAttribute::select('product_id')
            ->whereIn('size', $sizes)
            ->pluck('product_id')
            ->toArray();
            if(!empty($getProductIds)){
                $query->whereIn('id', $getProductIds);
            }
        }

        // Apply Brand Filter
        if(request()->has('brand') && !empty(request()->get('brand'))){
            $brands = explode('~', request()->get('brand'));
            $getBrandIds = Brand::select('id')
            ->whereIn('name', $brands)
            ->pluck('id')
            ->toArray();
            $query->whereIn('brand_id', $getBrandIds);
        }

        // Apply Price Filter
        if(request()->has('price') && !empty(request()->get('price'))){
            $priceInput = str_replace('~', '-', request()->get('price'));
            $prices = explode('-', $priceInput);
            $count = count($prices);
            if($count >= 2){
                $query->whereBetween('final_price', [(int)$prices[0], (int)$prices[$count - 1]]);
            }
        }

        // Apply Category Filter
       if(request()->has('category') && !empty(request()->get('category'))){
          $categoryIds = explode('~', request()->get('category'));
          $parentIds = Category::whereIn('parent_id', $categoryIds)->pluck('id')->toArray();
          $allCatIds = array_merge($categoryIds, $parentIds);
          if(!empty($allCatIds)){
              $query->whereIn('category_id', $allCatIds);
          }
       }

        // Apply Dynamic Admin Filters (fabric sleeve pattern etc.)
        $filterParams = request()->all();

        foreach($filterParams as $filterKey => $filterValues){
            // skip known default filters (color size brand price sort)
            if(in_array($filterKey, ['color', 'size', 'brand', 'price', 'sort', 'page', 'json', 'category', 'subCategory'])){
                continue;
            }

            // filter values can be "~" separated
            $selectedValues = explode('~', $filterValues);

            if(!empty($selectedValues)){
                $query->whereHas('filterValues', function($q) use ($selectedValues){
                    $q->whereIn('value', $selectedValues);
                });
        }  
    }

        return $query;
    }

    public function searchProducts($query, $limit = 6){
        
        $terms = explode(' ', str_replace(['-','_'], ' ', $query));

        return Product::with([
            'product_images' => function($q){
                $q->where('status', 1)->orderBy('sort', 'asc');
            }  
        ])
        ->where('status', 1)
        ->where('stock', '>', 0)
        ->where(function($q) use ($terms){
            foreach($terms as $term){
                if(!empty($term)){
                    $q->where('product_name', 'LIKE', '%'.$term.'%')
                      ->orWhere('product_code', 'LIKE', '%'.$term.'%')
                      ->orWhere('product_color', 'LIKE', '%'.$term.'%');
                }
            }
        })
        ->limit($limit)
        ->get();
    }

    public function getProductDetailsByUrl($url){
        return Product::with([
            'category.parentcategory', // for breadcrumbs
            'product_images' // for gallery
        ])
        ->where('status', 1)
        ->where('product_url', $url)
        ->firstOrFail();
    }
}