<?php

namespace Core;

use \App\Auth;
use \App\Flash;

// Main controller

// PHP version 7.4

abstract class Controller
{
    protected $route_params = [];
// Construct
    public function __construct($route_params)
    {
        $this->route_params = $route_params;
    }
    public function __call($name, $args)
    {
        $method = $name . 'Action';
        if (method_exists($this, $method))
        {
            if ($this->before() !== false) 
            {
                call_user_func_array([$this, $method], $args);
                $this->after();
            }
        } 
        else 
        {
            throw new \Exception("Method $method not found in controller " . get_class($this));
        }
    }

    protected function before()
    {
    }

    protected function after()
    {   
    }

    public function redirect($url)
    {
        header('Location: https://' . $_SERVER['HTTP_HOST'] . $url, true, 303);
        exit;
    }

    public function requireLogin()
    {
        if (! Auth::getUser()) 
        {
            $loginError = 'Proszę się zalogować';            
            $this->redirect('/login/new', 
            [
                'loginError' => $loginError
            ] );
        }
    }
}