<?php

namespace Core;

//Router
//PHP version 7.4

class Router
{
    protected $routes = [];
    protected $params = [];

    
    //Add routes and parameters to the routes table 
    public function add($route, $params = [])
    {
        // Convert the route to a regular expression
        $route = preg_replace('/\//', '\\/', $route);
        // Convert variables
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);
        // Convert variables with custom regular expressions
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);
        // Add start and end delimiters, and case insensitive flag
        $route = '/^' . $route . '$/i';
        $this->routes[$route] = $params;
    }

    //return routes from routing table
    public function getRoutes()
    {
        return $this->routes;
    }

    //Match url with routes and parameters
    public function match($url)
    {
        foreach ($this->routes as $route => $params) 
        {
            if (preg_match($route, $url, $matches)) 
            {
                // Get named capture group values
                foreach ($matches as $key => $match) 
                {
                    if (is_string($key)) 
                    {
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }
        return false;
    }

    //parameters to match
    public function getParams()
    {
        return $this->params;
    }

    //Use router dispatch method, create controller class and run method-action
    public function dispatch($url)
    {
        $url = $this->removeQueryStringVariables($url);

        if ($this->match($url)) 
        {
            $controller = $this->params['controller'];
            $controller = $this->convertToStudlyCaps($controller);
            $controller = $this->getNamespace() . $controller;
            
            if (class_exists($controller)) 
            {
                $controller_object = new $controller($this->params);
                $action = $this->params['action'];
                $action = $this->convertToCamelCase($action);

                if (is_callable([$controller_object, $action])) 
                {
                    $controller_object->$action();
                } 
                else 
                {
                    throw new \Exception("Method $action (in controller $controller) not found");
                }
            }
            else 
            {
                throw new \Exception("Controller class $controller not found");
            }
        }
        else 
        {
            throw new \Exception('No route matched.', 404);
        }
    }
    //Convert variables to StudlyCaps
    protected function convertToStudlyCaps($string)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }
    //Convert hyphens to camelCase string
    protected function convertToCamelCase($string)
    {
        return lcfirst($this->convertToStudlyCaps($string));
    }
    //remove query string variables
    protected function removeQueryStringVariables($url)
    {
        if ($url != '') 
        {
            $parts = explode('&', $url, 2);

            if (strpos($parts[0], '=') === false) 
            {
                $url = $parts[0];
            } 
            else 
            {
                $url = '';
            }
        }
        return $url;
    }
    //get namespace from proper directory
    protected function getNamespace()
    {
        $namespace = 'App\Controllers\\';
        if (array_key_exists('namespace', $this->params)) 
        {
            $namespace .= $this->params['namespace'] . '\\';
        }
        return $namespace;
    }
}