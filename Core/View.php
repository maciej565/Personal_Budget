<?php

namespace Core;

class View
{
    public static function render($view, $args = [])
    {
        extract($args, EXTR_SKIP);
        $file = dirname(__DIR__) . "/App/Views/$view";
        if (is_readable($file)) 
        {
            require $file;
        } 
        else 
        {
            throw new \Exception("$file not found");
        }
    }
    public static function renderTemplate($template, $args = [])
    {
        static $twig = null;

        if ($twig === null) 
        {
            $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/App/Views');
            $twig = new \Twig\Environment($loader);
            $twig->addGlobal('current_user', \App\Auth::getUser());
            
        }
        echo $twig->render($template, $args);
    }
    public static function getTemplate($template, $args = [])
    {
        static $twig = null;
        
        if ($twig === null) 
        {
            $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/App/Views');
            $twig = new \Twig\Environment($loader);
            $twig->addGlobal('current_user', \App\Auth::getUser());
        }
        return $twig->render($template, $args);
    }
}