<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use App\Models\Currency;

// Admin Controllers
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\FilterController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\FilterValueController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CartController as CartAdmin;

// Front Controllers
use App\Http\Controllers\Front\IndexController;
use App\Http\Controllers\Front\CartController;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Front\ProductController as ProductFrontController;
use App\Http\Controllers\Front\CouponController as CouponFrontController;
use App\Http\Controllers\Front\AuthController;
use App\Http\Controllers\Front\CurrencySwitchController;
use App\Http\Controllers\Front\ReviewController as ReviewFrontController;
use App\Http\Controllers\Front\AccountController;
use App\Http\Controllers\Front\PostcodeLookupController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;


Route::get('/', function () {
    return view('welcome');
    
});

Route::get('product-image/{size}/{filename}', function ($size, $filename) {
  $sizes = config('image_sizes.product');
  if(!isset($sizes[$size])) {
    abort(404, 'invalid size');
  }
  $width = $sizes[$size]['width'];
  $height = $sizes[$size]['height'];
  $path = public_path('front/images/products/' . $filename);
  if (!file_exists($path)) {
    abort(404, 'Image not found');
  }
  $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
  $image = $manager->read($path)
      ->resize($width, $height, function ($constraint) {
          $constraint->aspectRatio();
          $constraint->upsize();
      });
      $binary = $image->toJpeg(85); // Compression with 85% quality
  return Response::make($binary)->header('Content-Type', 'image/jpeg');
});
    

Route::prefix('admin')->group(function () {

    // Show the admin login form
      Route::get('login', [AdminController::class, 'create'])->name('admin.login');
    // Handle the admin login form submission
      Route::post('login', [AdminController::class, 'store'])->name('admin.login.request');

  Route::group(['middleware' => ['admin']], function () {

        //Route Dashboard
      Route::resource('dashboard', AdminController::class)->only(['index']);

      // Display Update Password
      Route::get('update-password', [AdminController::class, 'edit'])->name('admin.update-password');

      // Verify Password
      Route::post('verify-password', [AdminController::class, 'verifyPassword'])->name('admin.verify.password');

      // Update Password
      Route::post('admin/update-password', [AdminController::class, 'updatePasswordRequest'])->name('admin.update-password.request');

      // Display Update Details
      Route::get('update-details', [AdminController::class, 'editDetails'])->name('admin.update-details');

      // Update Details
      Route::post('admin/update-details', [AdminController::class, 'updateDetails'])->name('admin.update-details.request');

      // Delete Profile Image
      Route::post('delete-profile-image', [AdminController::class, 'deleteProfileImage']);

      // Subadmins
      Route::get('subadmins', [AdminController::class, 'subadmins']);
      Route::post('update-subadmin-status', [AdminController::class, 'updateSubadminStatus']);
      Route::get('add-edit-subadmin/{id?}', [AdminController::class, 'addEditSubadmin']);
      Route::post('add-edit-subadmin/request', [AdminController::class, 'addEditSubadminRequest']); 
      Route::get('delete-subadmin/{id}', [AdminController::class, 'deleteSubadmin']);
      // Roles
      Route::get('/update-role/{id}', [AdminController::class, 'updateRole']);
      Route::post('/update-role/request', [AdminController::class, 'updateRoleRequest']);

      
      // Categories
      Route::resource('categories', CategoryController::class);
      Route::post('update-category-status', [CategoryController::class, 'updateCategoryStatus']);
      Route::post('delete-category-image', [CategoryController::class, 'deleteCategoryImage']);
      Route::post('delete-sizechart-image', [CategoryController::class, 'deleteSizeChartImage']);

      // Products
      Route::resource('products', ProductController::class);
      Route::post('update-product-status', [ProductController::class, 'updateProductStatus']);

      // Product Main Image and Video
      Route::post('/product/upload-image', [ProductController::class, 'uploadImage'])->name('product.upload.image');
      // Product Gallery Images
      Route::post('/product/upload-images', [ProductController::class, 'uploadImages'])->name('product.upload.images');
      Route::post('/product/delete-temp-image', [ProductController::class, 'deleteTempImage'])->name('product.delete.temp.image');
      Route::get('delete-product-image/{id?}', [ProductController::class, 'deleteProductImage']);
      Route::post('/product/upload-video', [ProductController::class, 'uploadVideo'])->name('product.upload.video');
      Route::get('delete-product-main-image/{id?}', [ProductController::class, 'deleteProductMainImage']);
      Route::get('delete-product-video/{id?}', [ProductController::class, 'deleteProductVideo']);
      Route::post('/product/update-image-sorting', [ProductController::class, 'updateImageSorting'])
      ->name('admin.product.update-image-sorting');
      Route::post('/product/delete-image', [ProductController::class, 'deleteDropzoneImage'])->name('admin.product.delete-image');
      Route::post('/product/delete-temp-image', [ProductController::class, 'deleteTempProductImage'])->name('product.delete-temp-altimage');
      Route::post('/product/delete-temp-video', [ProductController::class, 'deleteTempProductVideo'])->name('product.delete.temp.video');

      // Filterz CRUD + status update
      Route::resource('filters', FilterController::class);
      Route::post('update-filter-status', [FilterController::class, 'updateFilterStatus'])->name('filters.updateStatus');

      // Filter Values CRUD (nested inside filters)
      // We map parameter name 'filter-values' => 'value' so request rtoute('value') works
      Route::prefix('filters/{filter}')->group(function(){
          Route::resource('filter-values', FilterValueController::class)->parameters(['filter-values' => 'value']);
      });
      
                                

      // Product Attributes
      Route::post('update-attribute-status', [ProductController::class, 'updateAttributeStatus']);
      Route::get('delete-product-attribute/{id}', [ProductController::class, 'deleteProductAttribute']);

     // Save Column Orders
      Route::post('/save-column-order', [AdminController::class, 'saveColumnOrder']);
      Route::post('/save-column-visibility', [AdminController::class, 'saveColumnVisibility']);

      // Brands
      Route::resource('brands', BrandController::class);
      Route::post('update-brand-status', [BrandController::class, 'updateBrandStatus']);

      // Banner
      Route::resource('banners', BannerController::class);
      Route::post('update-banner-status', [BannerController::class, 'updateBannerStatus']);

      // Coupons
      Route::resource('coupons', CouponController::class);
      Route::post('update-coupon-status', [CouponController::class, 'updateCouponStatus']);

      // Users
      Route::get('users', [UserController::class, 'index'])->name('users.index');
      Route::post('update-user-status', [UserController::class, 'updateUserStatus']);

      // Currencies
      Route::resource('currencies', CurrencyController::class);
      Route::post('update-currency-status', [CurrencyController::class, 'updateCurrencyStatus']);

      // Reviews
      Route::resource('reviews', ReviewController::class);
      Route::post('/update-review-status', [ReviewController::class, 'updateReviewStatus'])->name('admin.updateReviewStatus');

      //Route logout
      Route::get('logout', [AdminController::class, 'destroy'])->name('admin.logout');

      
  });

});

Route::namespace('App\Http\Controllers\Front')->group(function () {
    Route::get('/', [IndexController::class, 'index']);
  

    // Only register category routes if table exists
    if(Schema::hasTable('categories')){
      try{$catUrls = Category::where('status', 1)->pluck('url')->toArray();
      foreach ($catUrls as $url) {
        Route::get("/$url", [ProductFrontController::class, 'index']);
      }
      }catch(\Exception $e){
        //Ignore Errors during migration/seed
      }
    }

    // Product Detail Page
    if(Schema::hasTable('products')){
      try{
        $productUrls = Product::where('status', 1)->pluck('product_url')->toArray();
        foreach ($productUrls as $url) {
          Route::get("/$url", [ProductFrontController::class, 'detail']);
        }
      }catch(\Throwable $e){
        //Ignore Errors during migration/seed
      }
    }

    Route::post('/get-product-price', [ProductFrontController::class, 'getProductPrice']);

    Route::get('/search-products', [ProductFrontController::class, 'ajaxSearch'])->name('search.products');

    // Cart Routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::get('/cart/refresh', [CartController::class, 'refresh'])->name('cart.refresh');


    // Add to Cart
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');

    // Update Cart (PATCH cart{cart})
    Route::patch('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');

    // Delete Item
    Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');

    // Apply Coupon
    Route::post('/cart/apply-coupon', [CouponFrontController::class, 'apply'])->name('cart.apply.coupon');

    // Remove Coupon
    Route::post('/cart/remove-coupon', [CouponFrontController::class, 'remove'])->name('cart.remove.coupon');

    Route::post('/currency/switch', [CurrencySwitchController::class, 'switch'])->name('currency.switch');
    
    // User auth pages (login/register) only for guests, and logout / user pages only for auth users
    // In your web.php routes file
// User routes
    Route::prefix('user')->name('user.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.post');
        Route::get('register', [AuthController::class, 'showRegister'])->name('register');
        Route::post('register', [AuthController::class, 'register'])->name('register.post');
        Route::get('password/forgot', [AuthController::class, 'showForgotForm'])->name('password.forgot');
        Route::post('password/forgot', [AuthController::class, 'sendResetLink'])->name('password.forgot.post');
    });
});

// Password reset routes (using Laravel's default names)
   Route::get('user/password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
   Route::post('user/password/reset', [AuthController::class, 'resetPassword'])->name('password.update');

// Auth routes
  Route::middleware('auth')->group(function (){
    Route::post('/product/review', [ReviewFrontController::class, 'store'])->name('product.review.store');
    Route::get('user/account', [AccountController::class, 'showAccount'])->name('user.account');
    Route::post('user/account', [AccountController::class, 'updateAccount'])->name('user.account.update');
    Route::get('user/postcode-lookup/{postcode}', [PostcodeLookupController::class, 'lookup'])->name('user.postcode.lookup');

    Route::post('user/logout', [AuthController::class, 'logout'])->name('user.logout');
});
   Route::get('/debug-currency', function () {
    $sessionCode = Session::get('currency_code');
    $cookieCode = Cookie::get('currency_code');
    $current = null;
    try {
        $current = getCurrentCurrency() ? getCurrentCurrency()->toArray() : null;
    } catch (\Throwable $e) {
        $current = 'getCurrentCurrency() error: '.$e->getMessage();
    }
    return response()->json([
        'session' => $sessionCode,
        'cookie' => $cookieCode,
        'currentCurrency' => $current,
        'format100' => function_exists('formatCurrency') ? formatCurrency(100) : 'formatCurrency() missing'
    ]);
});
});




