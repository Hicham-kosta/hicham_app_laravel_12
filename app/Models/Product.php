<?php
namespace App\Models;

use App\Models\ProductsAttribute;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\ProductImage;
use Laravel\Scout\Searchable;
use App\Models\Brand;
use App\Models\Admin;

class Product extends Model
{
    use Searchable;

    protected $fillable = [
        // Existing fields...
        'admin_id',
        'admin_role',
        'category_id',
        'brand_id',
        'product_name',
        'product_code',
        'product_color',
        'family_color',
        'group_code',
        'product_weight',
        'product_price',
        'product_gst',
        'product_discount',
        'final_price',
        'product_discount_amount',
        'discount_applied_on',
        'is_featured',
        'sort',
        'vendor_id',
        'description',
        'wash_care',
        'search_keywords',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'main_image',
        'product_video',
        'product_url',
        'stock',
        'status',
        
        // New approval fields
        'is_approved',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'rejection_reason',
        'is_vendor_product',
        'vendor_product_status'
    ];

        /**
     * Cast attributes to native types
     */
    protected $casts = [
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'is_vendor_product' => 'boolean',
    ];
    
    /**
     * Get the admin who approved the product
     */
    public function approver()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }
    
    /**
     * Get the admin who rejected the product
     */
    public function rejector()
    {
        return $this->belongsTo(Admin::class, 'rejected_by');
    }
    
    /**
     * Scope a query to only include approved products
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }
    
    /**
     * Scope a query to only include pending products
     */
    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }
    
    /**
     * Scope a query to only include vendor products
     */
    public function scopeVendorProducts($query)
    {
        return $query->where('is_vendor_product', true);
    }
    
    /**
     * Scope a query to only include admin products
     */
    public function scopeAdminProducts($query)
    {
        return $query->where('is_vendor_product', false);
    }
    
    /**
     * Check if product is approved
     */
    public function isApproved()
    {
        return $this->is_approved == true;
    }
    
    /**
     * Check if product is pending approval
     */
    public function isPending()
    {
        return $this->is_approved == false && empty($this->rejection_reason);
    }
    
    /**
     * Check if product is rejected
     */
    public function isRejected()
    {
        return !empty($this->rejection_reason);
    }


    public function category(){
        return $this->belongsTo('App\Models\Category', 'category_id')->with('parentCategory');
    }

    public function brand(){
       return $this->belongsTo(Brand::class, 'brand_id');
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

    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class, 'product_id');
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

   public function otherCategories(){
       return $this->hasMany(ProductsCategory::class, 'product_id');
   }

   public static function getAttributePrice($product_id, $size){
    // 1- Attribute row for this size
      $attribute = ProductsAttribute::where([
        'product_id' => $product_id,
        'size'       => $size,
        'status'     => 1
      ])->first();
      if(!$attribute){
          return ['status' => false];
      }
      $basePrice = (float)$attribute->price;

      // 2- load product (MySQL: id)
      $product = self::select('id', 'category_id', 'brand_id', 'product_discount')
                    ->where('id', $product_id)
                    ->first();
      if(!$product){
        return ['status' => false];
      }

      // 3- Applicable discounts (product > category > brand)
      $productDisc = (float)($product->product_discount ?? 0);
      $categoryDisc = 0;
      if($product->category_id){
        $cat = Category::select('discount')->find($product->category_id);
        $categoryDisc = (float)($cat->discount ?? 0);
      }
      $brandDisc = 0;
      if($product->brand_id){
        // if your brands table doesn't have 'discount' this will just be 0
        $brand = Brand::select('discount')->find($product->brand_id);
        $brandDisc = (float)($brand->discount ?? 0);
      }
      $applied = 0;
         if($productDisc > 0 ){
            $applied = $productDisc;
         }elseif($categoryDisc > 0){
            $applied = $categoryDisc;
        }elseif($brandDisc > 0){
            $applied = $brandDisc;
        }
        $final = $applied > 0 
        ? round($basePrice - ($basePrice * $applied/100)) 
        : round($basePrice);
        $discountAmt = $basePrice - $final;
        return [
            'status' => true,
            'product_price' => (int)$basePrice, // numeric - base currency
            'final_price' => (int)$final, // numeric - base currency
            'discount' => (int)$discountAmt,
            'percent' => (int)$applied,
            // Formatted strings for display (int current selected currency)
            'product_price_formatted' => formatCurrency($basePrice),
            'final_price_formatted' => formatCurrency($final),
            // cCurrencience fields (old keys kept for backward compatibility)
            'product_price_display' => formatCurrency($basePrice),
            'final_price_display' => formatCurrency($final)  
        ];
   }

    
    public static function productStatus($product_id)
    {
        return self::where('id', $product_id)->value('status') ?? 0;
    }

    public function review()
    {
        return $this->hasMany(\App\Models\Review::class, 'product_id');
    }

    public function averageRating()
    {
        return (float)$this->reviews->where('status', 1)->avg('rating') ?? 0;
    }

    /**
     * Relationship with vendor
     */
    public function vendor()
    {
        return $this->belongsTo(Admin::class, 'vendor_id')->where('role', 'vendor');
    }

    /**
     * Scope for vendor's products
     */
    public function scopeForVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    /**
     * Scope for approved vendors only
     */
    public function scopeFromApprovedVendors($query)
    {
        return $query->whereHas('vendor.vendorDetails', function ($q) {
            $q->where('is_verified', 1);
        });
    }
}

