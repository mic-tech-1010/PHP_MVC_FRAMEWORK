<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../core/App.php';
require_once __DIR__ . '/../core/Route/Route.php';
require_once __DIR__ . '/../core/Helpers/DieAndDump.php';
require_once __DIR__ . '/../core/Helpers/Redirect.php';

use Core\App;
use App\Http\Kernel;

App::boot(__DIR__ . '/../public');

// // Load middleware registration
// require_once __DIR__ . '/middleware.php';

// ✅ Register middlewares
Kernel::registerMiddlewares();
