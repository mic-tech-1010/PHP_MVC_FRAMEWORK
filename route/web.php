<?php

use App\Http\Controllers\HomeController;
use Core\Route\Route;
use Core\Database\DB;
use Core\View\View;
use Core\App;

// Route::get('/', function () {
//     $data =  DB::table('user')
//         ->select('id', 'username', 'email')
//         ->where('id', '=', 2)
//         ->getAll();

//     echo "<pre>";
//     print_r($data);
// });


Route::get('/', [HomeController::class, 'index']);

Route::get('/about', [HomeController::class, 'about']);

Route::get('/contact', [HomeController::class, 'contact']);

Route::post('/contact', [HomeController::class, 'postContact']);

// Route::post('/profile/{id}', 'Profile:index', ['auth']);

// Route::group('/admin', function ($r) {

//     $r->get('/', 'admin.AdminController:index');
//     $r->get('/post/{id}/show', 'admin.PostController:index');
//     $r->post('/post', 'admin.post:create');

// },[]);

//Route::run();
