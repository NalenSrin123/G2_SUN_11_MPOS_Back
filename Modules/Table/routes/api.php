<?php

use Illuminate\Support\Facades\Route;
use Modules\Table\Http\Controllers\TableController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('tables', TableController::class)->names('table');
});
