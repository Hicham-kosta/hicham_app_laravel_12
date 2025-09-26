<?php
namespace App\Models;

use App\Models\ProductsAttribute;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\ProductImage;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use Searchable;

    public function category(){
        return $this->belongsTo('App\Models\Category', 'category_id')->with('parentCategory');
    }

    public function product_images(){
        return $this->hasMany(ProductImage::class)->orderBy('sort', 'asc');
    }

    public function attributes(){
        return $this->hasMany(ProductsAttribute::class);
    }

    public function filterValues(){
        return $this->belongsToMany(FilterValue::class, 'product_filter_values', 'product_id', 'filter_value_id');
    }

    public function toSearchableArray(){
        $categoryName = $this->category->name ?? null;
        return[
            'id'           => $this->id,
            'product_name' => $this->product_name,
            'category'     => $categoryName,
        ];
    }

    public function categories(){
        return $this->belongsToMany(Category::class, 'products_categories', 'product_id', 'category_id');
   }

}