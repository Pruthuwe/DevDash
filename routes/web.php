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
    $totalProducts = \App\Models\Product::count();
    $totalCustomers = \App\Models\Customer::count();
    $totalAppointments = \App\Models\Appointment::count();
    $totalBlogs = \App\Models\Blog::count();
    $totalServices = \App\Models\Service::count();
    $totalSuppliers = \App\Models\Supplier::count();
    $latestCustomers = \App\Models\Customer::latest()->take(5)->get();
    $latestProducts = \App\Models\Product::latest()->take(5)->get();

    return view('dashboard', compact(
        'totalProducts',
        'totalCustomers',
        'totalAppointments',
        'totalBlogs',
        'totalServices',
        'totalSuppliers',
        'latestCustomers',
        'latestProducts'
    ));
})->name('dashboard');
        
        // Product Routes
        Route::resource('products', \App\Http\Controllers\ProductController::class)->except(['index'])->middleware([
            'permission:view-products'
        ]);
        Route::get('/manage-products', [\App\Http\Controllers\ProductController::class, 'manage'])->middleware('permission:view-products')->name('manage.products');
        Route::get('/add-product', [\App\Http\Controllers\ProductController::class, 'create'])->middleware('permission:create-products')->name('add.product');
        Route::get('/categories/{category}/subcategories', [\App\Http\Controllers\CategoryController::class, 'getSubcategories'])->name('categories.subcategories');
        
    // Category Routes
    Route::resource('categories', \App\Http\Controllers\CategoryController::class)->except(['index'])->middleware([
        'permission:view-categories'
    ]);
    Route::get('/add-category', [\App\Http\Controllers\CategoryController::class, 'create'])->middleware('permission:create-categories')->name('add.category');
    Route::get('/manage-category', [\App\Http\Controllers\CategoryController::class, 'manage'])->middleware('permission:view-categories')->name('manage.category');
    Route::post('/store-subcategory', [\App\Http\Controllers\CategoryController::class, 'storeSubcategory'])->middleware('permission:create-categories')->name('store.subcategory');
    
    // Customer Routes
    Route::resource('customers', \App\Http\Controllers\CustomerController::class)->except(['index'])->middleware([
        'permission:view-customers'
    ]);
    Route::get('/add-customer', [\App\Http\Controllers\CustomerController::class, 'create'])->middleware('permission:create-customers')->name('add.customer');
    Route::get('/manage-customers', [\App\Http\Controllers\CustomerController::class, 'manage'])->middleware('permission:view-customers')->name('manage.customers');
    
    // Supplier Routes
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class)->except(['index'])->middleware([
        'permission:view-suppliers'
    ]);
    Route::get('/add-supplier', [\App\Http\Controllers\SupplierController::class, 'create'])->middleware('permission:create-suppliers')->name('add.supplier');
    Route::get('/manage-suppliers', [\App\Http\Controllers\SupplierController::class, 'manage'])->middleware('permission:view-suppliers')->name('manage.suppliers');
    
    // Purchase Routes
    Route::resource('purchases', \App\Http\Controllers\PurchaseController::class)->middleware([
        'permission:view-purchases'
    ]);
    Route::get('/add-purchase', [\App\Http\Controllers\PurchaseController::class, 'create'])->middleware('permission:create-purchases')->name('add.purchase');
    Route::get('/manage-purchases', [\App\Http\Controllers\PurchaseController::class, 'manage'])->middleware('permission:view-purchases')->name('manage.purchases');
    Route::get('/products/{id}/details', [\App\Http\Controllers\PurchaseController::class, 'getProductDetails'])->middleware('permission:view-products')->name('products.details');
    Route::get('/products/search', [\App\Http\Controllers\PurchaseController::class, 'searchProducts'])->middleware('permission:view-products')->name('products.search');
    
    // Quotation Routes
    Route::resource('quotations', \App\Http\Controllers\QuotationController::class)->middleware([
        'permission:view-quotations'
    ]);
    Route::get('/add-quotation', [\App\Http\Controllers\QuotationController::class, 'create'])->middleware('permission:create-quotations')->name('add.quotation');
    Route::get('/quotation-list', [\App\Http\Controllers\QuotationController::class, 'index'])->middleware('permission:view-quotations')->name('quotation.list');
    Route::get('/manage-quotations', [\App\Http\Controllers\QuotationController::class, 'index'])->middleware('permission:view-quotations')->name('manage.quotations');
    
    // Blog Routes
    Route::resource('blogs', \App\Http\Controllers\BlogController::class)->middleware([
        'permission:view-blogs'
    ]);
    Route::get('/blog-list', [\App\Http\Controllers\BlogController::class, 'index'])->middleware('permission:view-blogs')->name('blog.list');
    Route::get('/add-blog', [\App\Http\Controllers\BlogController::class, 'create'])->middleware('permission:create-blogs')->name('add.blog');
    Route::get('/manage-blogs', [\App\Http\Controllers\BlogController::class, 'index'])->middleware('permission:view-blogs')->name('manage.blogs');
    
    // Service Routes
    Route::resource('services', \App\Http\Controllers\ServiceController::class)->middleware([
        'permission:view-services'
    ]);
    Route::get('/service-list', [\App\Http\Controllers\ServiceController::class, 'index'])->middleware('permission:view-services')->name('service.list');
    Route::get('/add-service', [\App\Http\Controllers\ServiceController::class, 'create'])->middleware('permission:create-services')->name('add.service');
    Route::get('/manage-services', [\App\Http\Controllers\ServiceController::class, 'index'])->middleware('permission:view-services')->name('manage.services');
    
    // Appointment Routes
    Route::resource('appointments', \App\Http\Controllers\AppointmentController::class)->middleware([
        'permission:view-appointments'
    ])->except(['create', 'store']);
    Route::get('/manage-appointments', [\App\Http\Controllers\AppointmentController::class, 'manage'])->middleware('permission:view-appointments')->name('manage.appointments');
    Route::post('/appointments/{appointment}/update-status', [\App\Http\Controllers\AppointmentController::class, 'updateStatus'])->middleware('permission:edit-appointments')->name('appointments.update-status');
    Route::post('/appointments/import-json', [\App\Http\Controllers\AppointmentController::class, 'importFromJson'])->middleware('permission:create-appointments')->name('appointments.import-json');
    
    // Contact Messages Routes
Route::get('/manage-contacts', [\App\Http\Controllers\ContactController::class, 'manage'])->name('manage.contacts');
Route::post('/contacts/{contact}/mark-read', [\App\Http\Controllers\ContactController::class, 'markRead'])->name('contacts.mark-read');
Route::delete('/contacts/{contact}', [\App\Http\Controllers\ContactController::class, 'destroy'])->name('contacts.destroy');



    // Role Routes
    Route::resource('roles', \App\Http\Controllers\RoleController::class)->middleware([
        'permission:view-roles'
    ]);
    Route::get('/role-list', [\App\Http\Controllers\RoleController::class, 'index'])->middleware('permission:view-roles')->name('role.list');

    // User Routes
    Route::resource('users', \App\Http\Controllers\UserController::class)->middleware([
        'permission:view-users'
    ]);
    Route::get('/user-list', [\App\Http\Controllers\UserController::class, 'index'])->middleware('permission:view-users')->name('user.list');
    Route::get('/user-assign-roles', [\App\Http\Controllers\UserController::class, 'assignRoles'])->middleware('permission:edit-users')->name('users.assign-roles');
    Route::post('/update-user-role', [\App\Http\Controllers\UserController::class, 'updateUserRole'])->middleware('permission:edit-users')->name('users.update-role');
});

// Password Reset Routes (placeholder)
Route::get('/password/request', function () {
    return view('auth.forgot-password');
})->name('password.request');
