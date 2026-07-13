<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API routes for blogs and services (no authentication required, GET only)
Route::get('blogs', [\App\Http\Controllers\BlogController::class, 'index']);
Route::get('blogs/{id}', [\App\Http\Controllers\BlogController::class, 'show']);
Route::get('services', [\App\Http\Controllers\ServiceController::class, 'index']);
Route::get('services/{id}', [\App\Http\Controllers\ServiceController::class, 'show']);
Route::get('categories', [\App\Http\Controllers\CategoryController::class, 'apiIndex']);
Route::get('categories/{id}/subcategories', [\App\Http\Controllers\CategoryController::class, 'apiSubcategories']);
Route::get('categories', [\App\Http\Controllers\CategoryController::class, 'apiIndex']);
Route::get('categories/{id}/subcategories', [\App\Http\Controllers\CategoryController::class, 'apiSubcategories']);
Route::get('products', [\App\Http\Controllers\ProductController::class, 'index']);
Route::get('products/{product}', [\App\Http\Controllers\ProductController::class, 'show']);
Route::get('appointments', [\App\Http\Controllers\AppointmentController::class, 'index']);
Route::get('appointments/{appointment}', [\App\Http\Controllers\AppointmentController::class, 'show']);
Route::post('contacts', [\App\Http\Controllers\ContactController::class, 'store']);
Route::post('loan-inquiries', [\App\Http\Controllers\LoanInquiryController::class, 'store']);
Route::put('loan-inquiries/{loanInquiry}', [\App\Http\Controllers\LoanInquiryController::class, 'publicUpdate']);
Route::get('finance-companies', [\App\Http\Controllers\FinanceCompanyController::class, 'index']);
Route::get('loan-calculator-data', [\App\Http\Controllers\LoanCalculatorController::class, 'apiIndex']);
Route::get('products/{product}/loan-plans', [\App\Http\Controllers\BikeLoanPlanController::class, 'index']);
Route::post('product-enquiries', [\App\Http\Controllers\ProductEnquiryController::class, 'store']);

// customers login
Route::post('customer/login', [\App\Http\Controllers\CustomerAuthController::class, 'login']);
Route::post('customer/register', [\App\Http\Controllers\CustomerAuthController::class, 'register']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('customer/logout', [\App\Http\Controllers\CustomerAuthController::class, 'logout']);
    Route::get('customer/profile', [\App\Http\Controllers\CustomerAuthController::class, 'profile']);
});

// Protected user endpoint
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
