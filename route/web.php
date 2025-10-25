<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\HomeController;
use Core\Route\Route;
use App\Http\Controllers\Auth\AuthController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/about', [HomeController::class, 'about']);

Route::get('/contact', [HomeController::class, 'contact']);

Route::post('/contact', [HomeController::class, 'postContact']);

// group with prefix
Route::middleware(['auth'])->group(['prefix' => 'admin'], function() {
    Route::get('/', fn() => 'Admin Home');          
    Route::get('/users', fn() => 'Admin Users');
});

//Route::get('/login', [AdminController::class, 'index']);

Route::get('/login', [AuthController::class, 'showLoginForm']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegisterForm']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/logout', [AuthController::class, 'logout']);


