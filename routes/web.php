<?php

use App\Http\Controllers\TableController;
use Illuminate\Support\Facades\Route;
use  App\Http\Controllers\OrderController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/tables',         [TableController::class, 'index']);
Route::get('/tables/{id}',    [TableController::class, 'show']);
Route::post('/tables',        [TableController::class, 'store']);
Route::put('/tables/{id}',    [TableController::class, 'update']);
Route::delete('/tables/{id}', [TableController::class, 'destroy']);


Route::get('/orders',         [OrderController::class, 'index']);
Route::get('/orders/{id}',    [OrderController::class, 'show']);
Route::post('/orders',        [OrderController::class, 'store']);
Route::put('/orders/{id}',    [OrderController::class, 'update']);
Route::delete('/orders/{id}', [OrderController::class, 'destroy']);