<?php

session_start();

require_once __DIR__ . '/../bootstrap/app.php';

require_once __DIR__ . '/../route/web.php';

use Core\Route\Route;

// After all routes are defined, run the router
Route::init();



