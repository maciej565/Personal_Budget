<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;

class Home extends \Core\Controller
{
	protected function before()
	{	
	}
	
	protected function after()
	{
	}
	
	public function indexAction()
    {
        if (Auth::getUser() ) 
        {
            $this->redirect( '/Mainpage/mainpage' );
        }
        else 
        {
            $success = false;
            if (isset($_SESSION['success'])) 
            {
                $success = $_SESSION['success'];
                unset ($_SESSION['success']);
            }
            
            View::renderTemplate('Home/index.html', 
            [
                'success' => $success                
            ] ); 
        }
    }
}
