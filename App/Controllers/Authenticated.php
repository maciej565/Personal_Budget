<?php

namespace App\Controllers;

//Authenticated base
//PHP version 7.4

abstract class Authenticated extends \Core\Controller
{
    //Require authentication
    protected function before()
    {
        $this->requireLogin();
    }
}
