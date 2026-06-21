<?php

use Illuminate\Support\Facades\Route;
use Modules\Table\Http\Controllers\TableController;

Route::group([], function () {
    Route::resource('tables', TableController::class);
});
