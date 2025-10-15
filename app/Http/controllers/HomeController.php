<?php

namespace App\Http\Controllers;

//use Core\Database\DB;
use Core\View\View;

class HomeController
{
    public function index()
    {
        return View::render('layout.index', [
            'title' => 'My second View',
             'name' => 'michael'
        ]);
    }
}
