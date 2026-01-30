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
Route::get('products', [\App\Http\Controllers\ProductController::class, 'index']);
Route::get('products/{product}', [\App\Http\Controllers\ProductController::class, 'show']);

// Protected user endpoint
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
