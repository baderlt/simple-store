<?php
// routes/web.php (COMPLETE VERSION)

use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\DiscountController as AdminDiscountController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PromotionController;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/promotions', [PromotionController::class, 'index'])->name('promotions.index');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
// routes/web.php
Route::get('/products/search/suggestions', [ProductController::class, 'searchSuggestions'])
    ->name('products.search.suggestions');

///categories
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

// Cart (No login required)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::put('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
// Cart AJAX routes
Route::get('/cart/ajax', [CartController::class, 'getAjax'])->name('cart.ajax.get');
Route::put('/cart/ajax/update/{id}', [CartController::class, 'ajaxUpdate'])->name('cart.ajax.update');
Route::delete('/cart/ajax/remove/{id}', [CartController::class, 'ajaxRemove'])->name('cart.ajax.remove');

// Checkout (No login required)
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
// Direct checkout route
Route::get('/checkout/direct/{id}', [CheckoutController::class, 'direct'])->name('checkout.direct');
    

Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/order/success/{id}', [CheckoutController::class, 'success'])->name('order.success');

// About page
Route::view('/about', 'about')->name('about');

/*
|--------------------------------------------------------------------------
| Auth Routes (Laravel Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // User Orders
    Route::get('/my-orders', [OrderController::class, 'index'])->name('orders.index');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Products
    Route::resource('products', AdminProductController::class);
    Route::post('products/{id}/images', [AdminProductController::class, 'deleteImage'])->name('products.images.delete');
    Route::post('/products/images/{image}/set-primary', [AdminProductController::class, 'setPrimaryImage'])
    ->name('products.images.set-primary');
    
    // Categories
    Route::resource('categories', AdminCategoryController::class);
    
    // Orders
    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::put('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('orders/{order}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');
    
    // Discounts
    Route::resource('discounts', AdminDiscountController::class);
    Route::post('/discounts/store-with-override', [AdminDiscountController::class, 'storeWithOverride'])->name('discounts.storeWithOverride');
Route::post('/discounts/{discount}/update-with-override', [AdminDiscountController::class, 'updateWithOverride'])->name('discounts.updateWithOverride');
    
    // Settings
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
    Route::delete('/settings/logo', [AdminSettingController::class, 'deleteLogo'])->name('settings.delete-logo');
    Route::get('/settings/json', [AdminSettingController::class, 'getJson'])->name('settings.json');
    Route::put('/settings/{key}', [AdminSettingController::class, 'updateSingle'])->name('settings.update-single');
    Route::get('/settings/{key}', [AdminSettingController::class, 'getSetting'])->name('settings.get');

    Route::resource('banners', \App\Http\Controllers\Admin\BannerController::class);
    Route::patch('banners/{banner}/toggle', [\App\Http\Controllers\Admin\BannerController::class, 'toggle'])
        ->name('banners.toggle');
        
    Route::get('banners/{banner}/duplicate', [\App\Http\Controllers\Admin\BannerController::class, 'duplicate'])
        ->name('banners.duplicate');
        
    Route::get('banners/{banner}/preview', [\App\Http\Controllers\Admin\BannerController::class, 'preview'])
        ->name('banners.preview');

    Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');
});

// Redirect /admin to /admin/dashboard
Route::redirect('/admin', '/admin/dashboard');