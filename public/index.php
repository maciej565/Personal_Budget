<?php

//Front Controller
// PHP version 7.4

require_once dirname(__DIR__).'/vendor/autoload.php';


$loader = new \Twig\Loader\ArrayLoader();
$twig = new \Twig\Environment($loader);

// Errors, exceptions
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

//Start session
session_start();

//Routing
$router = new Core\Router();

// Router configuration

$router->add('',['controller' => 'Home', 'action' => 'index']);
$router->add('login', ['controller' => 'Login', 'action' => 'new']);

$router->add('{controller}/{action}');

$router->add('income', ['controller' => 'addIncome', 'action' => 'new']);
$router->add('addIncome', ['controller' => 'addIncome', 'action' => 'create']);

$router->add('{controller}/{id:\d+}/{action}');
$router->add('signup/activate/{token:[\da-f]+}', ['controller' => 'Signup', 'action' => 'activate']);
$router->add('logout', ['controller' => 'Login', 'action' => 'destroy']);

$router->add('signup', ['controller' => 'Signup', 'action' => 'new']);

$router->dispatch($_SERVER['QUERY_STRING']);