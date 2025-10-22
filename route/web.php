<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\HomeController;
use Core\Route\Route;

Route::get('/', [HomeController::class, 'index']);

Route::get('/about', [HomeController::class, 'about']);

Route::get('/contact', [HomeController::class, 'contact']);

Route::post('/contact', [HomeController::class, 'postContact']);

// group with prefix
Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function() {
    Route::get('/', fn() => 'Admin Home');          
    Route::get('/users', fn() => 'Admin Users');
});

Route::get('/login', [AdminController::class, 'index']);

