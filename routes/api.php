<?php
  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
  
use App\Http\Controllers\API\RegisterController;
  
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
  
Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);
  
Route::middleware('auth:api')->group( function () {
    Route::controller(App\Http\Controllers\API\ProductController::class)->middleware('auth')->group(function () {
        Route::get('products', 'index');
        Route::post('products/{id}', 'update');
        Route::get('products/{id}', 'show');
        Route::post('products-delete/{id}', 'destroy');
    });
});
Route::middleware('auth:api')->group( function () {
    Route::controller(App\Http\Controllers\API\UserController::class)->middleware('auth')->group(function () {
        Route::get('users', 'index');
        Route::post('users', 'store');
        Route::post('users/{id}', 'update');
        Route::get('users/{id}', 'show');
        Route::post('users-delete/{id}', 'destroy');
    });
});
