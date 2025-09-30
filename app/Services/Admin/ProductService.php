<?php

namespace App\Services\Admin;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductsAttribute;
use App\Models\AdminsRole;
use App\Models\Category;
use App\Models\ProductsCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductService
{
    public function products(){

        $products = Product::with('category')->get();

        // Set Admin/Subadmin Permissions for Products
        $productsModuleCount = AdminsRole::where(['subadmin_id' => Auth::guard('admin')->user()->id,
            'module' => 'products'])
            ->count();
            $status = "success";
            $message = "";
            $productsModule = [];

            if(Auth::guard('admin')->user()->role == 'admin'){
                $productsModule = [
                    'view_access' => 1,
                    'edit_access' => 1,
                    'full_access' => 1,
                ];
            }elseif($productsModuleCount == 0){
                $status = "error";
                $message = "You don't have access to this module.";
            }else{
                $productsModule = AdminsRole::where(['subadmin_id' => Auth::guard('admin')->user()->id,
                    'module' => 'products'])
                    ->first()->toArray();
            }
            return [
                'status' => $status,
                'message' => $message,
                'productsModule' => $productsModule,
                'products' => $products
            ];
   }

   public function updateProductStatus($data){
    $status = ($data['status'] == "Active") ? 0 : 1;
    Product::where('id', $data['product_id'])->update(['status' => $status]);
    return $status;
   }

   public function deleteProduct($id){
    Product::where('id', $id)->delete();
    $message = "Product has been deleted successfully.";
    return ['message' => $message];
   }

   public function addEditProduct($request){

    $data = $request->all();

    if(isset($data['id']) && $data['id'] != ""){
        // Update Product
        $product = Product::find($data['id']);
        $message = "Product has been updated successfully.";
    }
    else{
        // Add Product
        $product = new Product;
        $message = "Product has been added successfully.";
    }
    $product->admin_id = Auth::guard('admin')->user()->id;
    $product->admin_role = Auth::guard('admin')->user()->role;

    $product->category_id = $data['category_id'];
    $product->brand_id = $data['brand_id'];
    $product->product_name = $data['product_name'];
    $product->product_code = $data['product_code'];
    $product->product_color = $data['product_color'];
    $product->family_color = $data['family_color'];
    $product->group_code = $data['group_code'];
    $product->product_weight = $data['product_weight'] ?? 0;
    $product->product_price = $data['product_price'];
    $product->product_gst = $data['product_gst'] ?? 0;
    $product->product_discount = $data['product_discount'] ?? 0;
    $product->is_featured = $data['is_featured'] ?? 'No';
    $product->sort = $data['sort'] ?? 0;

    // Calculate Discount && Final Price
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
    $product->search_keywords = $data['search_keywords'] ?? '';
    $product->meta_title = $data['meta_title'] ?? '';
    $product->meta_keywords = $data['meta_keywords'] ?? '';
    $product->meta_description = $data['meta_description'] ?? '';
    $product->status = 1;

    // Upload Main Image
    if(!empty($data['main_image_hidden'])){
        $sourcePath = public_path('temp/' . $data['main_image_hidden']);
        $destinationPath = public_path('front/images/products/' . $data['main_image_hidden']);
        if(file_exists($sourcePath)){
            @copy($sourcePath, $destinationPath);
            @unlink($sourcePath);
      } 

      $product->main_image = $data['main_image_hidden'];
    }

    // Upload Product Video
    if(!empty($data['product_video_hidden'])){
        $sourcePath = public_path('temp/' . $data['product_video_hidden']);
        $destinationPath = public_path('front/videos/products/' . $data['product_video_hidden']);
        if(file_exists($sourcePath)){
            @copy($sourcePath, $destinationPath);
            @unlink($sourcePath);
        }

        $product->product_video = $data['product_video_hidden'];
    }

    $product->main_image = $request->main_image ?? $product->main_image;
    $product->product_video = $request->product_video ?? $product->product_video;
    $product->product_url = $product->product_url ?? null;

    $product->save();

    // Generate product_url only if create mode
    if($product->wasRecentlyCreated){
        $slug = Str::slug($data['product_name']);
        $product->product_url = $slug.'-'.$product->id;
        $product->save();
    }else{
        // in update mode, update product_url id provided
        if(!empty($data['product_url'])){
            $product->product_url = Str::slug($data['product_url']);
            $product->save();
        }
    }

    // Sync other categories for this product
    if(!empty($data['other_categories']) && is_array($data['other_categories'])){
        // Clear other records
        ProductsCategory::where('product_id', $product->id)->delete();

        // Insert new records
        foreach($data['other_categories'] as $catId){
            ProductsCategory::create([
                'product_id' => $product->id,
                'category_id' => $catId,
            ]);
        }
    }else{
        // Clear other records
        ProductsCategory::where('product_id', $product->id)->delete();
    }

    // Sync filter values for this product
    if(!empty($data['filter_values']) && is_array($data['filter_values'])){
        // $data['filter_values'] = [filter_id => filter_value_id]']);
        // keep only selected values (non empty)
        // (This block appears to be incomplete and can be removed or implemented as needed.)
    }

    // sync filter values for this product
    if(!empty($data['filter_values']) && is_array($data['filter_values'])){
        // $data['filter_values'] = [filter_id => filter_value_id]']);
        // keep only selected values (non empty)
        $values = array_values(array_filter($data['filter_values']));
        $product->filterValues()->sync($values);
    } else{
        $product->filterValues()->detach();
    }

    // Upload Alternative Images
    if(!empty($data['product_images'])){
        // Ensure we have an array of images
        $imageFiles = is_array($data['product_images']) 
        ? $data['product_images'] 
        : explode(',', $data['product_images']);

        // Remove any empty values
        $imageFiles = array_filter($imageFiles);

        foreach($imageFiles as $index => $filename){
            $sourcePath = public_path('temp/' . $filename);
            $destinationPath = public_path('front/images/products/' . $filename);

            if(file_exists($sourcePath)){
                @copy($sourcePath, $destinationPath);
                @unlink($sourcePath);
            }

            ProductImage::create([
                'product_id' => $product->id,
                'image' => $filename,
                'sort' => $index + 1, // Sort starts from 1
                'status' => 1, // Default status is active
            ]);
        }
    }

    $totalStock = 0;
    foreach($data['sku'] as $key => $value){
        if(!empty($value)&& !empty($data['size'][$key]) && !empty($data['price'][$key])){
            // SKU is already exists
            $attrCountSKU = ProductsAttribute::join('products', 'products_attributes.id', '=', 'products.id')
                ->where('sku', $value)->count();
            if($attrCountSKU > 0){
                $message = "SKU already exists. Please choose a different SKU.";
                return redirect()->back()->with('success_message', $message);
        }
            // Size is already exists
            $attrCountSize = ProductsAttribute::join('products', 'products_attributes.id', '=', 'products.id')
                ->where(['product_id' => $product->id, 'size' => $data['size'][$key]])->count();
            if($attrCountSize > 0){
                $message = "Size already exists. Please choose a different Size.";
                return redirect()->back()->with('success_message', $message);
        }
        if(empty($data['stock'][$key])){
            $data['stock'][$key] = 0;
        }
        $attribute = new ProductsAttribute;
        $attribute->product_id = $product->id;
        $attribute->sku = $value;
        $attribute->size = $data['size'][$key];
        $attribute->price = $data['price'][$key];
        if(!empty($data['stock'][$key])){
            $attribute->stock = $data['stock'][$key];
        }
        $attribute->sort = $data['sort'][$key];
        $attribute->status = 1; // Default status is active
        $attribute->save();
        $totalStock += $data['stock'][$key];
        }
    }
    //Edit Product Attributes
    if(isset($data['id']) && $data['id']!="" && isset($data['attrId'])){
        foreach($data['attrId'] as $key => $attr){
            if(!empty($attr)){
                $update_attr = [
                  'price' => $data['update_price'][$key],
                  'stock' => $data['update_stock'][$key],
                  'sort' => $data['update_sort'][$key],  
                ];
                ProductsAttribute::where(['id' => $data['attrId'][$key]])->update($update_attr);
            }
        }
    }
    // Update Product Stock in Editable Product
    if(isset($data['attrId'])){
        foreach($data['attrId'] as $attrKeyId => $attrIdDetails){
            $proAttrUpdate = ProductsAttribute::find($attrIdDetails);
            $proAttrUpdate->stock = $data['update_stock'][$attrKeyId];
            $totalStock += $data['update_stock'][$attrKeyId];
        }
    }
    Product::where('id', $product->id)->update(['stock' => $totalStock]);

    return $message;
}

    public function updateAttributeStatus($data){
      $status = ($data['status'] == "Active") ? 0 : 1;
      ProductsAttribute::where('id', $data['attribute_id'])->update(['status' => $status]);
      return $status;
   }
   

   public function handleImageUpload($file){
    $imageName = time() . '.' . rand(1111,9999) . '.' . $file->getClientOriginalExtension();
    $file->move(public_path('front/images/products'), $imageName);
    return $imageName;
   }

   public function handleVideoUpload($file){
    $videoName = time() . '.' . $file->getClientOriginalExtension();
    $file->move(public_path('front/videos/products'), $videoName);
    return $videoName;
   }

   public function deleteProductMainImage($id){
    // Get Product Main Image
    $product = Product::select('main_image')->where('id', $id)->first();
    if(!$product || !$product->main_image){
        return "No image found to delete.";
    }

    // Get Product Main Image Path
    $image_path = public_path('front/images/products/'.$product->main_image);

    // Delete Product Main Image if exist
    if(file_exists($image_path)){
        unlink($image_path);   
   }

   // Delete Product Image from Database
    Product::where('id', $id)->update(['main_image' => '']);
    $message = "Product image has been deleted successfully.";

    return $message;
}

   public function deleteProductImage($id){
    // Get Product Images
    $product = ProductImage::select('image')->where('id', $id)->first();
    if(!$product || !$product->image){
        return "No image found to delete.";
    }

    // Get Product Main Image Path
    $image_path = public_path('front/images/products/'.$product->image);

    // Delete Product Main Image if exist
    if(file_exists($image_path)){
        unlink($image_path);   
   }

   // Delete Product Image from Database
    ProductImage::where('id', $id)->delete();
    $message = "Product image has been deleted successfully.";

    return $message;
   }

   public function deleteProductVideo($id){
    // Get Product Video
    $productVideo = Product::select('product_video')->where('id', $id)->first();
    
    // Get Product Video Path
    $product_video_path = public_path('/front/videos/products/'. $productVideo->product_video);

    // Delete Product Video if exist
    if(file_exists($product_video_path)){
        unlink($product_video_path);   
    }

    // Delete Product Video from Database
    Product::where('id', $id)->update(['product_video' => '']);
    $message = "Product video has been deleted successfully.";
    return $message;
   }

    public function deleteProductAttribute($id){
     // Delete Product Attribute from Database
     ProductsAttribute::where('id', $id)->delete();
     return "Product attribute has been deleted successfully!";
    }

    public function updateImageSorting(array $sortedImages): void{
        foreach($sortedImages as $imageData){
            if(isset($imageData['id']) && isset($imageData['sort'])){
                ProductImage::where('id', $imageData['id'])->update([
                    'sort' => $imageData['sort']
                ]);
            }
        }
    }

    public function deleteDropzoneImage(string $imageName): bool{
        $imagePath = public_path('front/images/products' . $imageName);
        return file_exists($imagePath) ? unlink($imagePath) : false;
    }

    public function deleteDropzoneVideo(string $filename): bool{
        $videoPath = public_path('front/videos/products' . $filename);
        return file_exists($videoPath) ? unlink($videoPath) : false;
    }
       

}