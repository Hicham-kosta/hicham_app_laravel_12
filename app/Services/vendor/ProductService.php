<?php

namespace App\Services\Vendor;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductsAttribute;
use App\Models\Category;
use App\Models\ProductsCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function products()
    {
        $vendor = Auth::guard('admin')->user();
        
        // Vendor KYC not approved
        if(!$vendor->vendorDetails || (int)$vendor->vendorDetails->is_verified === 0){
            return [
                'products' => collect(),
                'productsModule' => [],
                'status' => 'error',
                'message' => 'Your vendor account is not approved yet. You can not add or manage products.',
            ];
        }
        
        // Vendor can see only his own products
        $products = Product::with('category')
            ->where('vendor_id', $vendor->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Vendor has full access to his own products
        $productsModule = [
            'view_access' => 1,
            'edit_access' => 1,
            'full_access' => 1,
        ];

        return [
            'products' => $products,
            'productsModule' => $productsModule,
            'status' => 'success',
            'message' => '',
        ];
    }

    public function updateProductStatus($data)
    {
        $vendor = Auth::guard('admin')->user();
        $status = ($data['status'] == "Active") ? 0 : 1;
        
        Product::where('id', $data['product_id'])
            ->where('vendor_id', $vendor->id)
            ->update(['status' => $status]);
            
        return $status;
    }

    public function deleteProduct($id)
    {
        $vendor = Auth::guard('admin')->user();
        
        Product::where('id', $id)
            ->where('vendor_id', $vendor->id)
            ->delete();
            
        $message = "Product has been deleted successfully.";
        return ['message' => $message];
    }

    public function addEditProduct($request)
{
    $vendor = auth('admin')->user();
    
    // Check if vendor is approved
    if (!$vendor->vendorDetails || $vendor->vendorDetails->is_verified != 1) {
        throw new \Exception('Your vendor account is not approved yet.');
    }

    $data = $request->input();

    $data['vendor_id'] = $vendor->id;
    
    // Handle product creation/update
    if(isset($data['id']) && $data['id'] != ""){
        // Update Product - ensure vendor owns this product
        $product = Product::where('id', $data['id'])
            ->where('vendor_id', $vendor->id)
            ->first();
            
        if (!$product) {
            throw new \Exception('Product not found or you do not have permission to edit it.');
        }
            
        $message = "Product has been updated successfully. Waiting for admin approval.";
    }
    else{
        // Add Product - vendor products start as inactive
        $product = new Product;
        $message = "Product has been added successfully. Waiting for admin approval.";
    }
    
    // Set vendor-specific fields
    $product->vendor_id = $vendor->id;
    $product->admin_id = 0;
    $product->admin_role = 'vendor';
    
    // Common product fields
    $product->category_id = $data['category_id'] ?? null;
    $product->brand_id = $data['brand_id'] ?? null;
    $product->product_name = $data['product_name'] ?? '';
    $product->product_code = $data['product_code'] ?? '';
    $product->product_color = $data['product_color'] ?? '';
    $product->family_color = $data['family_color'] ?? '';
    $product->group_code = $data['group_code'] ?? '';
    $product->product_weight = $data['product_weight'] ?? 0;
    $product->product_price = $data['product_price'] ?? 0;
    $product->product_gst = $data['product_gst'] ?? 0;
    $product->product_discount = $data['product_discount'] ?? 0;
    $product->is_featured = $data['is_featured'] ?? 'No';
    $product->sort = $data['sort'] ?? 0;
    
    // Vendor products require admin approval
    $product->status = 0;
    $product->is_approved = 0;
    
    // Calculate Discount & Final Price
    if(!empty($data['product_discount']) && $data['product_discount'] > 0){
        $product->discount_applied_on = 'product';
        $product->product_discount_amount = ($data['product_price'] * $data['product_discount']) / 100;
    } else{
        $getCategoryDiscount = Category::select('discount')->where('id', $data['category_id'])->first();
        if($getCategoryDiscount && $getCategoryDiscount->discount > 0){
            $product->discount_applied_on = 'category';
            $product->product_discount = $getCategoryDiscount->discount;
            $product->product_discount_amount = ($data['product_price'] * $getCategoryDiscount->discount) / 100;
        } else{
            $product->discount_applied_on = '';
            $product->product_discount_amount = 0;
        }
    }
    $product->final_price = $data['product_price'] - $product->product_discount_amount;

    // Optional Fields
    $product->description = $data['description'] ?? '';
    $product->wash_care = $data['wash_care'] ?? '';
    $product->search_keywords = $data['search_keywords'] ?? '';
    $product->meta_title = $data['meta_title'] ?? '';
    $product->meta_keywords = $data['meta_keywords'] ?? '';
    $product->meta_description = $data['meta_description'] ?? '';

    // ============ HANDLE MAIN IMAGE UPLOAD ============
    if($request->hasFile('main_image')){
        $mainImage = $request->file('main_image');
        $imageName = time() . '_' . uniqid() . '.' . $mainImage->getClientOriginalExtension();
        
        // Create directory if it doesn't exist
        $directory = public_path('front/images/products');
        if(!file_exists($directory)){
            mkdir($directory, 0777, true);
        }
        
        $mainImage->move($directory, $imageName);
        $product->main_image = $imageName;
    } elseif(!empty($data['main_image_hidden'])) {
    $product->main_image = $data['main_image_hidden'];
}

        // Handle temp uploaded images
        $tempPath = public_path('temp/' . $data['main_image_hidden']);
        $destinationPath = public_path('front/images/products/' . $data['main_image_hidden']);
        
        if(file_exists($tempPath)){
            @copy($tempPath, $destinationPath);
            @unlink($tempPath);
            $product->main_image = $data['main_image_hidden'];
        }
    


    // ============ HANDLE PRODUCT VIDEO UPLOAD ============
    if($request->hasFile('product_video')){
        $productVideo = $request->file('product_video');
        $videoName = time() . '_' . uniqid() . '.' . $productVideo->getClientOriginalExtension();
        
        // Create directory if it doesn't exist
        $directory = public_path('front/videos/products');
        if(!file_exists($directory)){
            mkdir($directory, 0777, true);
        }
        
        $productVideo->move($directory, $videoName);
        $product->product_video = $videoName;
    } elseif(!empty($data['product_video_hidden'])) {
    $product->product_video = $data['product_video_hidden'];
}

        // Handle temp uploaded videos
        $tempPath = public_path('temp/' . $data['product_video_hidden']);
        $destinationPath = public_path('front/videos/products/' . $data['product_video_hidden']);
        
        if(file_exists($tempPath)){
            @copy($tempPath, $destinationPath);
            @unlink($tempPath);
            $product->product_video = $data['product_video_hidden'];
        }
    
    
    $product->save();

    // ============ HANDLE ALTERNATIVE IMAGES UPLOAD ============
    // Handle multiple file uploads
    if($request->hasFile('product_images')){
        $productImages = $request->file('product_images');
        
        foreach($productImages as $index => $image){
            $imageName = time() . '_' . uniqid() . '_' . $index . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('front/images/products'), $imageName);

            ProductImage::create([
                'product_id' => $product->id,
                'image' => $imageName,
                'sort' => $index,
                'status' => 1,
            ]);
        }
    }
    
    // Handle temp uploaded alternative images
    if(!empty($data['product_images_hidden'])){
    $imageFiles = is_array($data['product_images_hidden'])
        ? $data['product_images_hidden']
        : explode(',', $data['product_images_hidden']);

    foreach($imageFiles as $index => $filename){
        ProductImage::create([
            'product_id' => $product->id,
            'image' => $filename,
            'sort' => $index,
            'status' => 1,
        ]);
    }
}


    // Generate product_url only if create mode
    if($product->wasRecentlyCreated){
        $slug = Str::slug($data['product_name']);
        $product->product_url = $slug.'-'.$product->id;
        $product->save();
    }elseif(!empty($data['product_url'])){
        // In update mode, update product_url if provided
        $product->product_url = Str::slug($data['product_url']);
        $product->save();
    }

    // Sync other categories for this product
    if(!empty($data['other_categories']) && is_array($data['other_categories'])){
        ProductsCategory::where('product_id', $product->id)->delete();
        
        foreach($data['other_categories'] as $catId){
            ProductsCategory::create([
                'product_id' => $product->id,
                'category_id' => $catId,
            ]);
        }
    } else {
        ProductsCategory::where('product_id', $product->id)->delete();
    }

    // Sync filter values for this product
    if(!empty($data['filter_values']) && is_array($data['filter_values'])){
        $values = array_values(array_filter($data['filter_values']));
        $product->filterValues()->sync($values);
    } else{
        $product->filterValues()->detach();
    }

    // Handle new product attributes
    $totalStock = 0;
    if(isset($data['sku']) && is_array($data['sku'])){
        foreach($data['sku'] as $key => $value){
            if(!empty($value) && !empty($data['price'][$key])){
                // Check if SKU already exists for this vendor
                $attrCountSKU = ProductsAttribute::join('products', 'products_attributes.product_id', '=', 'products.id')
                    ->where('products.vendor_id', $vendor->id)
                    ->where('sku', $value)
                    ->count();
                    
                if($attrCountSKU > 0 && !isset($data['id'])){
                    throw new \Exception("SKU already exists. Please choose a different SKU.");
                }
                
                $attribute = new ProductsAttribute;
                $attribute->product_id = $product->id;
                $attribute->sku = $value;
                $attribute->size = $data['size'][$key] ?? '';
                $attribute->price = $data['price'][$key] ?? 0;
                $attribute->stock = $data['stock'][$key] ?? 0;
                $attribute->sort = $data['sort'][$key] ?? 0;
                $attribute->status = 1;
                $attribute->save();
                
                $totalStock += $attribute->stock;
            }
        }
    }
    
    // Edit existing product attributes
    if(isset($data['attrId']) && is_array($data['attrId'])){
        foreach($data['attrId'] as $key => $attrId){
            if(!empty($attrId)){
                $attribute = ProductsAttribute::find($attrId);
                if($attribute && $attribute->product_id == $product->id){
                    $attribute->sku = $data['update_sku'][$key] ?? $attribute->sku;
                    $attribute->size = $data['update_size'][$key] ?? $attribute->size;
                    $attribute->price = $data['update_price'][$key] ?? $attribute->price;
                    $attribute->stock = $data['update_stock'][$key] ?? $attribute->stock;
                    $attribute->sort = $data['update_sort'][$key] ?? $attribute->sort;
                    $attribute->save();
                    
                    $totalStock += $attribute->stock;
                }
            }
        }
    }
    
    // Update total stock
    $product->stock = $totalStock;
    $product->save();

    return $message;
}

    public function updateAttributeStatus($data)
    {
        $vendor = Auth::guard('admin')->user();
        $status = ($data['status'] == "Active") ? 0 : 1;
        
        ProductsAttribute::where('id', $data['attribute_id'])
            ->whereHas('product', function($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id);
            })
            ->update(['status' => $status]);
            
        return $status;
    }

    public function handleImageUpload($file)
    {
        $imageName = time() . '.' . rand(1111,9999) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('front/images/products'), $imageName);
        return $imageName;
    }

    public function handleVideoUpload($file)
    {
        $videoName = time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('front/videos/products'), $videoName);
        return $videoName;
    }

    public function deleteProductMainImage($id)
    {
        $vendor = Auth::guard('admin')->user();
        
        $product = Product::where('id', $id)
            ->where('vendor_id', $vendor->id)
            ->first();
            
        if(!$product || !$product->main_image){
            return "No image found to delete.";
        }

        $image_path = public_path('front/images/products/'.$product->main_image);
        
        if(file_exists($image_path)){
            unlink($image_path);   
        }

        Product::where('id', $id)
            ->where('vendor_id', $vendor->id)
            ->update(['main_image' => '']);
            
        return "Product image has been deleted successfully.";
    }

    public function deleteProductImage($id)
    {
        $vendor = Auth::guard('admin')->user();
        
        $productImage = ProductImage::where('id', $id)
            ->whereHas('product', function($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id);
            })
            ->first();
            
        if(!$productImage || !$productImage->image){
            return "No image found to delete.";
        }

        $image_path = public_path('front/images/products/'.$productImage->image);
        
        if(file_exists($image_path)){
            unlink($image_path);   
        }

        ProductImage::where('id', $id)->delete();
        return "Product image has been deleted successfully.";
    }

    public function deleteProductVideo($id)
    {
        $vendor = Auth::guard('admin')->user();
        
        $productVideo = Product::where('id', $id)
            ->where('vendor_id', $vendor->id)
            ->first();
        
        if(!$productVideo || !$productVideo->product_video){
            return "No video found to delete.";
        }

        $video_path = public_path('/front/videos/products/'. $productVideo->product_video);
        
        if(file_exists($video_path)){
            unlink($video_path);   
        }

        Product::where('id', $id)
            ->where('vendor_id', $vendor->id)
            ->update(['product_video' => '']);
            
        return "Product video has been deleted successfully.";
    }

    public function deleteProductAttribute($id)
    {
        $vendor = Auth::guard('admin')->user();
        
        ProductsAttribute::where('id', $id)
            ->whereHas('product', function($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id);
            })
            ->delete();
            
        return "Product attribute has been deleted successfully!";
    }

    public function updateImageSorting(array $sortedImages): void
    {
        foreach($sortedImages as $imageData){
            if(isset($imageData['id']) && isset($imageData['sort'])){
                ProductImage::where('id', $imageData['id'])->update([
                    'sort' => $imageData['sort']
                ]);
            }
        }
    }

    public function deleteDropzoneImage(string $imageName): bool
    {
        $imagePath = public_path('front/images/products/' . $imageName);
        return file_exists($imagePath) ? unlink($imagePath) : false;
    }

    public function deleteDropzoneVideo(string $filename): bool
    {
        $videoPath = public_path('front/videos/products/' . $filename);
        return file_exists($videoPath) ? unlink($videoPath) : false;
    }
    
    // New method to get vendor's approval status
    public function getVendorApprovalStatus()
    {
        $vendor = Auth::guard('admin')->user();
        
        return [
            'is_verified' => $vendor->vendorDetails && $vendor->vendorDetails->is_verified == 1,
            'message' => $vendor->vendorDetails && $vendor->vendorDetails->is_verified == 1 
                ? '' 
                : 'Your vendor account is not approved yet.'
        ];
    }
}