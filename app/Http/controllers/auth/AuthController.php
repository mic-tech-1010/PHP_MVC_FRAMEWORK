<?php

namespace App\Http\Controllers\Auth;

use Core\Database\DB;
use Core\Http\Session;
use Core\View\View;
use Core\Http\Request;
use Core\Auth\Auth;

class AuthController
{
    public function showLoginForm()
    {
        return view::render('auth.login');
    }

    public function showRegisterForm()
    {
        return view::render('auth.register');
    }

    public function register(Request $request)
    {
        $name = trim($request->username);
        $email = trim($request->email);
        $password = password_hash($request->password, PASSWORD_DEFAULT);

        //check if email exists
        $existing = DB::table('user')
            ->select('email')
            ->where('email', '=', $email)
            ->get();

        if ($existing) {
            Session::start();
            Session::set('_flash.error', 'Email already registered');
            return redirect('/register');
        }

        DB::table('user')->insert([
            'username' => $name,
            'email' => $email,
            'password' => $password,
            'gender' => "male",
            'date' => date("Y-m-d H:i:s")
        ]);

        // auto-login new user
        $user = DB::table('user')
            ->select('email')
            ->where('email', '=', $email)
            ->get();
            
        Session::start();
        Session::set('user', $user);
        return redirect('/admin');
    }

    public function login(Request $request)
    {
        $email = trim($request->email);
        $password = $request->password;

        $user = DB::table('user')->select('*')->where('email', '=', $email)->get();

        if (!$user || !password_verify($password, $user->password)) {
            Session::start();
            Session::set('_flash.error', 'Invalid credentials');
            return redirect('/login');
        }

        Session::start();
        Session::set('user', $user);
        return redirect('/admin');
    }

    public function logout()
    {
        Session::start();
        Session::destroy();
        return redirect('/login');
    }
}
