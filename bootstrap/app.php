<?php


require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../core/App.php';
require_once __DIR__ . '/../core/Route/Route.php';

use Core\App;

App::boot(__DIR__ . '/../public');

//return App::class;