<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Vendor\ProductService;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use App\Models\ProductImage;
use App\Models\ProductsAttribute;
use App\Http\Requests\Admin\ProductRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    protected $productService;
    
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Session::put('page', 'products');
        $result = $this->productService->products();
        
        if($result['status'] == 'error'){
            return redirect('vendor/dashboard')->with('error_message', $result['message']);
        }

        $products = $result['products'];
        $productsModule = $result['productsModule'];

        return view('vendor.products.index')->with(compact(
            'products', 
            'productsModule'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Check vendor approval status
        $approvalStatus = $this->productService->getVendorApprovalStatus();
        if(!$approvalStatus['is_verified']){
            return redirect()->route('vendor.products.index')
                ->with('error_message', $approvalStatus['message']);
        }
        
        $title = "Add Product";
        $getCategories = Category::getCategories('Vendor');
        $brands = Brand::where('status', 1)->get()->toArray();
        
        return view('vendor.products.add_edit_product', 
            compact('title', 'getCategories', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $approvalStatus = $this->productService->getVendorApprovalStatus();
        if(!$approvalStatus['is_verified']){
            return redirect()->route('vendor.products.index')
                ->with('error_message', $approvalStatus['message']);
        }
        
        try {
            // Validate the request
            $validatedData = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'brand_id' => 'nullable|exists:brands,id',
                'product_name' => 'required|string|max:255',
                'product_code' => 'required|string|max:100|unique:products,product_code',
                'product_color' => 'nullable|string|max:100',
                'family_color' => 'nullable|string|max:100',
                'group_code' => 'nullable|string|max:100',
                'product_weight' => 'nullable|numeric|min:0',
                'product_price' => 'required|numeric|min:0',
                'product_gst' => 'nullable|numeric|min:0|max:100',
                'product_discount' => 'nullable|numeric|min:0|max:100',
                'is_featured' => 'required|in:Yes,No',
                'sort' => 'nullable|integer',
                'description' => 'nullable|string',
                'wash_care' => 'nullable|string',
                'search_keywords' => 'nullable|string',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'meta_keywords' => 'nullable|string',
                'size.*' => 'nullable|string',
                'sku.*' => 'nullable|string',
                'price.*' => 'nullable|numeric|min:0',
                'stock.*' => 'nullable|integer|min:0',
                'sort.*' => 'nullable|integer',
            ]);
            
            // Add vendor_id to validated data
            $validatedData['vendor_id'] = Auth::guard('admin')->user()->id;
            
            // Handle file uploads
            if($request->hasFile('main_image')) {
                $validatedData['main_image'] = $request->file('main_image');
            }
            
            if($request->hasFile('product_images')) {
                $validatedData['product_images'] = $request->file('product_images');
            }
            
            if($request->hasFile('product_video')) {
                $validatedData['product_video'] = $request->file('product_video');
            }
            
            // Handle other categories
            if($request->has('other_categories')) {
                $validatedData['other_categories'] = $request->input('other_categories');
            }
            
            // Handle filter values
            if($request->has('filter_values')) {
                $validatedData['filter_values'] = $request->input('filter_values');
            }
            
            // Create a new request with validated data
            $request->merge($validatedData);
            $request->merge([
    'main_image_hidden' => $request->input('main_image_hidden'),
    'product_images_hidden' => $request->input('product_images_hidden'),
    'product_video_hidden' => $request->input('product_video_hidden'),
]);



            $message = $this->productService->addEditProduct($request);

            return redirect()->route('vendor.products.index')->with('success_message', $message);
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error_message', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $approvalStatus = $this->productService->getVendorApprovalStatus();
        if(!$approvalStatus['is_verified']){
            return redirect()->route('vendor.products.index')
                ->with('error_message', $approvalStatus['message']);
        }
        
        $vendor = Auth::guard('admin')->user();
        $title = 'Edit Product';
        
        $product = Product::with(['product_images', 'attributes', 'otherCategories', 'filterValues'])
            ->where('id', $id)
            ->where('vendor_id', $vendor->id)
            ->firstOrFail();
            
        $getCategories = Category::getCategories('Vendor');
        $brands = Brand::where('status', 1)->get()->toArray();
        
        return view('vendor.products.add_edit_product', 
            compact('title', 'product', 'getCategories', 'brands'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $approvalStatus = $this->productService->getVendorApprovalStatus();
        if(!$approvalStatus['is_verified']){
            return redirect()->route('vendor.products.index')
                ->with('error_message', $approvalStatus['message']);
        }
        
        try {
            // First check if product belongs to vendor
            $vendor = Auth::guard('admin')->user();
            $product = Product::where('id', $id)
                ->where('vendor_id', $vendor->id)
                ->firstOrFail();
            
            // Validate the request
            $validatedData = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'brand_id' => 'nullable|exists:brands,id',
                'product_name' => 'required|string|max:255',
                'product_code' => 'required|string|max:100|unique:products,product_code,' . $id,
                'product_color' => 'nullable|string|max:100',
                'family_color' => 'nullable|string|max:100',
                'group_code' => 'nullable|string|max:100',
                'product_weight' => 'nullable|numeric|min:0',
                'product_price' => 'required|numeric|min:0',
                'product_gst' => 'nullable|numeric|min:0|max:100',
                'product_discount' => 'nullable|numeric|min:0|max:100',
                'is_featured' => 'required|in:Yes,No',
                'sort' => 'nullable|integer',
                'description' => 'nullable|string',
                'wash_care' => 'nullable|string',
                'search_keywords' => 'nullable|string',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'meta_keywords' => 'nullable|string',
                'product_url' => 'nullable|string|max:255',
                'update_size.*' => 'nullable|string',
                'update_sku.*' => 'nullable|string',
                'update_price.*' => 'nullable|numeric|min:0',
                'update_stock.*' => 'nullable|integer|min:0',
                'update_sort.*' => 'nullable|integer',
                'size.*' => 'nullable|string',
                'sku.*' => 'nullable|string',
                'price.*' => 'nullable|numeric|min:0',
                'stock.*' => 'nullable|integer|min:0',
                'sort.*' => 'nullable|integer',
            ]);
            
            // Add ID for update
            $validatedData['id'] = $id;
            $validatedData['vendor_id'] = $vendor->id;
            
            // Handle file uploads
            if($request->hasFile('main_image')) {
                $validatedData['main_image'] = $request->file('main_image');
            }
            
            if($request->hasFile('product_images')) {
                $validatedData['product_images'] = $request->file('product_images');
            }
            
            if($request->hasFile('product_video')) {
                $validatedData['product_video'] = $request->file('product_video');
            }
            
            // Handle other categories
            if($request->has('other_categories')) {
                $validatedData['other_categories'] = $request->input('other_categories');
            }
            
            // Handle filter values
            if($request->has('filter_values')) {
                $validatedData['filter_values'] = $request->input('filter_values');
            }
            
            // Handle existing attributes
            if($request->has('update_size')) {
                $validatedData['update_size'] = $request->input('update_size');
                $validatedData['update_sku'] = $request->input('update_sku');
                $validatedData['update_price'] = $request->input('update_price');
                $validatedData['update_stock'] = $request->input('update_stock');
                $validatedData['update_sort'] = $request->input('update_sort');
                $validatedData['attrId'] = $request->input('attrId', []);
            }
            
            // Handle new attributes
            if($request->has('size')) {
                $validatedData['size'] = $request->input('size');
                $validatedData['sku'] = $request->input('sku');
                $validatedData['price'] = $request->input('price');
                $validatedData['stock'] = $request->input('stock');
                $validatedData['sort'] = $request->input('sort');
            }
            
            // Create a new request with validated data
            $request->merge($validatedData);
            $request->merge([
    'main_image_hidden' => $request->input('main_image_hidden'),
    'product_images_hidden' => $request->input('product_images_hidden'),
    'product_video_hidden' => $request->input('product_video_hidden'),
]);

           $message = $this->productService->addEditProduct($request);

            return redirect()->route('vendor.products.index')->with('success_message', $message);
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error_message', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $approvalStatus = $this->productService->getVendorApprovalStatus();
        if(!$approvalStatus['is_verified']){
            return redirect()->route('vendor.products.index')
                ->with('error_message', $approvalStatus['message']);
        }
        
        $result = $this->productService->deleteProduct($id);
        return redirect()->back()->with('success_message', $result['message']);
    }

    // ... keep the rest of your methods the same
    public function updateProductStatus(Request $request)
    {
        if($request->ajax()){
            $data = $request->all();
            $status = $this->productService->updateProductStatus($data);
            return response()->json(['status' => $status, 'product_id' => $data['product_id']]);
        }
    }

    public function updateAttributeStatus(Request $request)
    {
        if($request->ajax()){
            $data = $request->all();
            $status = $this->productService->updateAttributeStatus($data);
            return response()->json(['status' => $status, 'attribute_id' => $data['attribute_id']]);
        }
    }

    // In Vendor/ProductController.php

public function uploadImage(Request $request)
{
    if($request->hasFile('main_image')){
        $file = $request->file('main_image');
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        
        // Save to temp directory
        $file->move(public_path('temp'), $fileName);
        
        return response()->json(['fileName' => $fileName]);
    }
    return response()->json(['error' => 'No file uploaded'], 400);
}

public function uploadImages(Request $request)
{
    if($request->hasFile('product_images')){
        $files = $request->file('product_images');
        $fileNames = [];
        
        foreach($files as $file){
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('temp'), $fileName);
            $fileNames[] = $fileName;
        }
        
        return response()->json(['fileName' => implode(',', $fileNames)]);
    }
    return response()->json(['error' => 'No files uploaded'], 400);
}

public function uploadVideo(Request $request)
{
    if($request->hasFile('product_video')){
        $file = $request->file('product_video');
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        
        // Save to temp directory
        $file->move(public_path('temp'), $fileName);
        
        return response()->json(['fileName' => $fileName]);
    }
    return response()->json(['error' => 'No video uploaded'], 400);
}
    public function deleteProductMainImage($id)
    {
        $message = $this->productService->deleteProductMainImage($id);
        return redirect()->back()->with('success_message', $message);
    }

    public function deleteProductImage($id)
    {
        $message = $this->productService->deleteProductImage($id);
        return redirect()->back()->with('success_message', $message);
    }

    public function deleteProductVideo($id)
    {
        $message = $this->productService->deleteProductVideo($id);
        return redirect()->back()->with('success_message', $message);
    }

    public function deleteProductAttribute($id)
    {
        $message = $this->productService->deleteProductAttribute($id);
        return redirect()->back()->with('success_message', $message);
    }
    
    public function updateImageSorting(Request $request){
        $this->productService->updateImageSorting($request->sorted_images);
        return response()->json(['status' => 'success']);
    }

    public function deleteDropzoneImage(Request $request)
    {
        $deleted = $this->productService->deleteDropzoneImage($request->image);
        return response()->json(['status' => $deleted ? 'deleted' : 'file not found'], $deleted ? 200 : 404);
    }

    public function deleteTempProductImage(Request $request)
    {
        $deleted = $this->productService->deleteDropzoneImage($request->filename);
        return response()->json(['status' => $deleted ? 'deleted' : 'file not found'], $deleted ? 200 : 404);
    }

    public function deleteTempProductVideo(Request $request)
    {
        $deleted = $this->productService->deleteDropzoneVideo($request->filename);
        return response()->json(['status' => $deleted ? 'deleted' : 'file not found'], $deleted ? 200 : 404);
    }
}