<?php

require_once __DIR__ . '/../bootstrap/app.php';

require_once __DIR__ . '/../route/web.php';

use Core\Route\Route;

// After all routes are defined, run the router
Route::init();

// use Core\Database\DB;

// $data =  DB::table('user')
//            ->select('id', 'username', 'email')
//            ->where('id', '=', 2)
//            ->getAll();
           // ->delete();
        //    ->andWhere('username', '=', 'sola')
        //    ->orWhere('password', '=', 'tade00')
        //    ->limit(2)
        //    ->orderBy('id')
        //    ->getAll();

//     $data = DB::table('user')->insert([
//     "username" => "made",
//     "email" => "made@gmail.com",
//     "password" => "made00",
//     "gender" => "male",
//     "date" => date("Y-m-d H:i:s")
// ]);

// echo "<pre>";
// print_r($data);
