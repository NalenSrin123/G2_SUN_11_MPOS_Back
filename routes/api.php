<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\Http\Controllers\CategoryController;
use Modules\Product\Http\Controllers\ProductController;

Route::prefix('v1')->group(function () {
    Route::apiResource('categories', CategoryController::class)->names('category');
    Route::apiResource('products', ProductController::class)->names('product');
});
