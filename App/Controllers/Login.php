<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;

// Login Controller
//PHP version 7.4

class Login extends \Core\Controller
{

    //Render login page
    public function newAction()
    {
        if (Auth::getUser() ) 
        {
            $this->redirect( '/Mainpage/mainpage' );
        }
        else 
        {
            $success = false;
            if (isset($_SESSION['success'])) {
                $success = $_SESSION['success'];
                unset ($_SESSION['success']);
            }
            
            View::renderTemplate('Login/new.html', [
                'success' => $success                
            ] ); 
        }
    }

    // Logging in user
    public function createAction()
    {
        $user = User::authenticate($_POST['email'], $_POST['password']);
        $remember_me = isset($_POST['remember_me']);
        
        if ( $user ) 
        {
            $success = false;
            Auth::login( $user, $remember_me);
            $this->redirect('/Mainpage/mainpage');           

        } 
        else 
        {
            $loginError = 'Podano niepoprawne dane logowania!';
            View::renderTemplate('Login/new.html', 
            [
                'email' => $_POST['email'],
                'loginError' => $loginError
            ] );
        }
    }
    
   
   // Logging out user
    public function destroyAction() 
    {
        Auth::logout();
        $this->redirect( '' );   
    }   
}