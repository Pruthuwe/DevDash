<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Authentication Routes
Route::get('/', [AuthController::class, 'showLoginForm'])->name('home');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Protected Routes (require authentication)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
        })->name('dashboard');
        
        // Product Routes
        Route::resource('products', \App\Http\Controllers\ProductController::class);
        Route::get('/product-list', [\App\Http\Controllers\ProductController::class, 'index'])->name('product.list');
        Route::get('manage-products', [\App\Http\Controllers\ProductController::class, 'manage'])->name('manage.products');
        // Temporarily make add-product accessible without auth for testing
        Route::get('/add-product', [\App\Http\Controllers\ProductController::class, 'create'])->name('add.product');
        Route::get('/categories/{category}/subcategories', [\App\Http\Controllers\CategoryController::class, 'getSubcategories'])->name('categories.subcategories');
        
    // Category Routes
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    Route::get('/add-category', [\App\Http\Controllers\CategoryController::class, 'create'])->name('add.category');
    Route::get('/category-list', [\App\Http\Controllers\CategoryController::class, 'index'])->name('category.list');
    Route::get('/manage-category', [\App\Http\Controllers\CategoryController::class, 'manage'])->name('manage.category');
    Route::post('/store-subcategory', [\App\Http\Controllers\CategoryController::class, 'storeSubcategory'])->name('store.subcategory');
    
    // Customer Routes
    Route::resource('customers', \App\Http\Controllers\CustomerController::class);
    Route::get('/customer-list', [\App\Http\Controllers\CustomerController::class, 'index'])->name('customer.list');
    Route::get('/add-customer', [\App\Http\Controllers\CustomerController::class, 'create'])->name('add.customer');
    Route::get('/manage-customers', [\App\Http\Controllers\CustomerController::class, 'manage'])->name('manage.customers');
    
    // Supplier Routes
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);
    Route::get('/supplier-list', [\App\Http\Controllers\SupplierController::class, 'index'])->name('supplier.list');
    Route::get('/add-supplier', [\App\Http\Controllers\SupplierController::class, 'create'])->name('add.supplier');
    Route::get('/manage-suppliers', [\App\Http\Controllers\SupplierController::class, 'manage'])->name('manage.suppliers');
    
    // Purchase Routes
    Route::resource('purchases', \App\Http\Controllers\PurchaseController::class);
    Route::get('/purchase-list', [\App\Http\Controllers\PurchaseController::class, 'index'])->name('purchase.list');
    Route::get('/add-purchase', [\App\Http\Controllers\PurchaseController::class, 'create'])->name('add.purchase');
    Route::get('/manage-purchases', [\App\Http\Controllers\PurchaseController::class, 'manage'])->name('manage.purchases');
    Route::get('/products/{id}/details', [\App\Http\Controllers\PurchaseController::class, 'getProductDetails'])->name('products.details');
    Route::get('/products/search', [\App\Http\Controllers\PurchaseController::class, 'searchProducts'])->name('products.search');
    
    // Quotation Routes
    Route::resource('quotations', \App\Http\Controllers\QuotationController::class);
    Route::get('/add-quotation', [\App\Http\Controllers\QuotationController::class, 'create'])->name('add.quotation');
    Route::get('/quotation-list', [\App\Http\Controllers\QuotationController::class, 'index'])->name('quotation.list');
    Route::get('/manage-quotations', [\App\Http\Controllers\QuotationController::class, 'index'])->name('manage.quotations');
    
    // Blog Routes
    Route::resource('blogs', \App\Http\Controllers\BlogController::class);
    Route::get('/blog-list', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.list');
    Route::get('/add-blog', [\App\Http\Controllers\BlogController::class, 'create'])->name('add.blog');
    Route::get('/manage-blogs', [\App\Http\Controllers\BlogController::class, 'index'])->name('manage.blogs');
    
    // Service Routes
    Route::resource('services', \App\Http\Controllers\ServiceController::class);
    Route::get('/service-list', [\App\Http\Controllers\ServiceController::class, 'index'])->name('service.list');
    Route::get('/add-service', [\App\Http\Controllers\ServiceController::class, 'create'])->name('add.service');
    Route::get('/manage-services', [\App\Http\Controllers\ServiceController::class, 'index'])->name('manage.services');
    });

// Password Reset Routes (placeholder)
Route::get('/password/request', function () {
    return view('auth.forgot-password');
})->name('password.request');
