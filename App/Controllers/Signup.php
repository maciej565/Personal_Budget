<?php

namespace App\Controllers;
use \Core\View;
use \App\Models\User;
use \App\Auth;

//Signup controller
//PHP version 7.4

class Signup extends \Core\Controller
{
	public function newAction()
    {
        if (Auth::getUser() )
        {
            $this->redirect( '/Mainpage/mainpage' );  
        }
        else
        {
            View::renderTemplate('Signup/new.html');
        }
    }

	public function createAction()
    {
        $user = new User($_POST);

        if (! $user->save())
        { 
            View::renderTemplate('/Signup/new.html', 
            [
                'user' => $user
            ]); 
        } 
        else
        {
            $user->saveUserData();
            $_SESSION['success'] = true;
            $user->sendActivationEmail();
            $this->redirect('/signup/success');
        }
    }

	public function successAction()
    {
        $activationInfo =  "Rejestracja przebiegła pomyslnie! Na podany w formularzu adres e-mail wysłany został link aktywacyjny";
        View::renderTemplate('Signup/new.html',
            [
                'activationInfo' => $activationInfo
            ]);
    }

    public function activateAction()
    {
        User::activate($this->route_params['token']);
        $this->redirect('/Signup/activated');
    }

    public function activatedAction()
    {
        View::renderTemplate('Signup/activated.html');
    }
    
}