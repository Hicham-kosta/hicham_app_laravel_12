<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Services\Front\ProductService;
use Illuminate\Support\Facades\Route;


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
        $url = Route::current()->uri();
        $category = Category::where('url', $url)->where('status', 1)->first();
        if (!$category) {
            abort(404, 'Category not found');
        }
        $data = $this->productService->getCategoryListingData($url);

        //if it's an Ajax (filters.js adds &json), return JSON with rendred view
        if(request()->has('json')){
            $view = view('front.products.ajax_products_listing', $data)->render();
            return response()->json(['view' => $view]);
        }
        return view('front.products.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function ajaxSearch(Request $request){

        $query = trim($request->get('q'));
        if(strlen($query) < 3){
            return ''; // Avoid searching for very short strings
        }
        $usedAlgolia = false;
        // step 1 DB search
        $products = $this->productService->searchProducts($query, 6);
        // step 2 fallback to Algolia
        if($products->isEmpty()){

            $isAlgoliaConfigured = 
                config('scout.driver') === 'algolia' && 
                !empty(env('ALGOLIA_APP_ID')) &&
                !empty(env('ALGOLIA_SECRET'));
            if($isAlgoliaConfigured){
                $products = Product::search($query)->take(6)->get();
                $usedAlgolia = true;
            }                                                              
        }

        // step 3 Build HTML

        $output = '';
        foreach ($products as $product) {
            if(!empty($product->main_image)){
                $image = asset('front/images/products/' . $product->main_image);
        }elseif(!empty($product->product_images) && $product->product_images->isNotEmpty()){
            $image = asset('front/images/products/' . $product->product_images->first()->image);
        }else{
            $image = asset('front/images/products/no-image.jpg');
        }
    
    $output .= '
       <div class="search-result-item py-2 border-bottom" style="max-width: 600px;
       margin: 10px;">
         <div class="row no-gutters align-items-center">
           <div class="col-auto pr-2">
            <a href="'.url('product/'.$product->id).'">
            <img src="'.$image.'"alt="'.$product->product_name.'"
            style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;">
            </a>
            </div>
           <div class="col">
             <h6 class="mb-1" style="font-size: 14px; white-space: nowrap; 
             overflow: hidden; text-overflow: ellipsis;">
             '.$product->product_name.'
             </h6>
             <div class="d-flex align-items-center">
               <span class="text-primary font-weight-bold">Tk'.$product->final_price.
               '</span>';
               if($product->product_discount > 0){
                $output .= '<small class="text-muted ml-2"><del>Tk'.$product->product_price.'
                </del></small>';
               }
        $output .= '
            </div>
          </div>
        <div class="col-auto pl-2 text-right">
          <a href="'.url('product/'.$product->id).'" class="btn btn-sm btn-outline-primary"
          title="View">
          <i class="fas fa-eye"></i>
          </a>
          <a href="javascript:void(0)" data-id="'.$product->id.'" 
          class="btn btn-sm btn-outline-success addToCartBtn" 
          title="Add to Cart">
          <i class="fas fa-shopping-cart"></i>
          </a>
        </div>
      </div>
    </div>';      
        }

        // Algolia badge if applicable
        if($usedAlgolia && $products->count()){
            $output .= '<div class="text-right mt-2">
            <a href="htttps://www.algolia.com" target="_blank" rel="noopener">
            <img src="/front/images/algolia.png" alt="search by algolia" style="hight:
            20px;">
            </a>
            </div>';
        }

        return $output ? : '<div align="center">No results found</div>';
    }
}
