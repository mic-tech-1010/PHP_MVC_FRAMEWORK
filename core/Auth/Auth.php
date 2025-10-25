<?php

namespace Core\Auth;

use Core\Http\Session;

class Auth
{
    public static function user()
    {
        Session::start();
        return Session::get('user');
    }

    public static function id()
    {
        return self::user()->id ?? null;
    }

    public static function check(): bool
    {
        return !is_null(self::user());
    }

    public static function logout()
    {
        Session::start();
        Session::destroy();
    }
}
