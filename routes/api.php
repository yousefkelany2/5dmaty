<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TestimonialController;

Route::prefix('auth')->group(function () {
     #User
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::put('update', [AuthController::class, 'updateProfile']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
    #services
    Route::get('services', [ServiceController::class, 'index']);
    Route::get('services/{id}', [ServiceController::class, 'show']);
    #Category
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{id}', [CategoryController::class, 'show']);
    #Order
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}/status', [OrderController::class, 'checkStatus']);
    #Testimonial
    Route::get('/testimonials', [TestimonialController::class, 'index']);



});

Route::prefix('auth')->middleware(['auth:api', 'admin'])->group(function () {
    #Categories
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::post('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    #Services
    Route::post('/services', [ServiceController::class, 'store']);
    Route::post('/services/{id}', [ServiceController::class, 'update']);
    Route::delete('/services/{id}', [ServiceController::class, 'destroy']);
    #Orders
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
    Route::get('orders', [OrderController::class, 'index']);
     #Testimonial
    Route::put('/testimonials/{id}/approve', [TestimonialController::class, 'approve']);

});

Route::prefix('auth')->middleware('auth:api')->group(function () {
     #Testimonial
    Route::post('/testimonials', [TestimonialController::class, 'store']);

});
