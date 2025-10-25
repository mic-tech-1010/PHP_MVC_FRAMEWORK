<?php

if (!function_exists('session')) {
    function session($key = null, $value = null)
    {
        // Ensure the session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Setter: session('key', 'value')
        if (!is_null($value)) {
            $_SESSION[$key] = $value;
            return true;
        }

        // Getter: session('key')
        if (!is_null($key)) {
            return $_SESSION[$key] ?? null;
        }

        // No args: return all session data
        return $_SESSION;
    }
}

if (!function_exists('session_unset_key')) {
    function session_unset_key($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
}
