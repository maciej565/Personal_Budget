<?php

namespace App;

use App\Models\User;
use App\Models\RememberedLogin;

// Authentication 
//PHP version 7.4

class Auth
{
    public static function login($user)
    {
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user->id;

    }
    
    //Logout, end session
    public static function logout()
    {
        //Unset session variables
        $_SESSION = [];
        //delete cookies
        if (ini_get('session.use_cookies')) 
        {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        //end session
        session_destroy();
        static::forgetLogin();        
    }
    //remember originally requested page
    public static function rememberRequestedPage()
    {
        $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
    }
    //get originally requested page
    public static function getReturnToPage()
    {
        return $_SESSION['return_to'] ?? '/';
    }
    //get actually logged user
    public static function getUser() 
    {
        if ( isset( $_SESSION['user_id'] ) ) 
        {
            return User::findByID( $_SESSION['user_id'] );
        }
    }
    //Login user from remembered cookie
    protected static function loginFromRememberCookie()
    {
        $cookie = $_COOKIE['remember_me'] ?? false;
        if ($cookie) 
        {
            $remembered_login = RememberedLogin::findByToken($cookie);
            if ($remembered_login && ! $remembered_login->hasExpired()) 
            {
                $user = $remembered_login->getUser();
                static::login($user, false);
                return $user;
            }
        }
    }
    //Remember login?
    protected static function forgetLogin()
    {
        $cookie = $_COOKIE['remember_me'] ?? false;
        if ($cookie) 
        {
            $remembered_login = RememberedLogin::findByToken($cookie);
            if ($remembered_login)
            {
                $remembered_login->delete();
            }
            setcookie('remember_me', '', time() - 3600);
        }
    }
}
