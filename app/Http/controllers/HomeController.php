<?php

namespace App\Http\Controllers;

use Core\Database\DB;
use Core\Route\Route;
use Core\Http\Request;
use Core\View\View;
use Core\App;

class HomeController
{
    public function index(Request $request)
    {
        return View::render('home', [
            'title' => 'My second View',
            'name' => 'michael'
        ]);
    }

    public function about(Request $request)
    {
        return View::render('about', [
            'title' => 'My second View',
            'name' => 'michael'
        ]);
    }

    public function contact(Request $request)
    {
        return View::render('contact');
    }

    public function postContact(Request $request)
    {
       $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email',
        ]);
 
        DB::table('user')->where('id', '=', 2)->update([
          'username' => $request->name,
          'email' => $request->email
        ]);

        return redirect(route('home'));

    }
}
