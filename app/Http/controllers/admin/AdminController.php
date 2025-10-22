<?php

namespace App\Http\Controllers\Admin;
use Core\View\View;

class AdminController {

    public function index () {
        return View::render('auth.login');
    }

}