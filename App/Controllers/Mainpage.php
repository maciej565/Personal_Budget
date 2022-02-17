<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Models\Balance;
use \App\Auth;
use \App\Flash;

class Mainpage extends Authenticated
{
	protected function before()
    {
        parent::before();

        $this->user = Auth::getUser();
    }
    
	public function mainpageAction()
	{
		$currentMonthDate = Balance::getCurrentMonthDate();
		$lastMonthDate = Balance::getlastMonthDate();
		$currentMonthBalance = Balance::getBalance($currentMonthDate,$this->user->id);
		$lastMonthBalance = Balance::getBalance($lastMonthDate,$this->user->id);
		$username = User::getUsername($this->user->id);
		
		View::renderTemplate('Mainpage/mainpage.html',
			[
				'user' => $this->user,
				'username' => $username,				
				'currentMonthBalance' => $currentMonthBalance,
				'lastMonthBalance' => $lastMonthBalance
			]);
	}
	
	
	/*
	
	
	public function currentmonthAction()
	{	
		$currentmonth = date('m');
		$currentyear = date('Y');
		$daysnumber = date('t');
		
		$_SESSION['datefrom'] = "$currentyear".'-'."$currentmonth".'-01';
		$_SESSION['dateto'] = "$currentyear".'-'."$currentmonth".'-'."$daysnumber";
		
		View::renderTemplate('Menu/showBalance.html');
	}
	
	public function previousmonthAction()
	{	
		$previousmonth = date('m') -1;
		if($previousmonth < 10)
		{
			$previousmonth = '0'."$previousmonth";
		}
		$previousmonthdaysnumber = date('t', strtotime("-1 MONTH"));
		if($previousmonth == 12)
		{
		$year = date('Y') -1;
		}
		else $year = date('Y');
		
		$_SESSION['datefrom'] =  "$year".'-'."$previousmonth".'-01';
		$_SESSION['dateto'] =  "$year".'-'."$previousmonth".'-'."$previousmonthdaysnumber";
		
		View::renderTemplate('Menu/showBalance.html');
	}
	
	public function currentyearAction()
	{	
		$currentyear = date('Y');
		$_SESSION['datefrom'] = "$currentyear".'-01-01';
		$_SESSION['dateto'] = "$currentyear".'-12-31';
		
		View::renderTemplate('Menu/showBalance.html');
	}
	
	public function customAction()
	{	
		$_SESSION['datefrom'] = $_POST['datefrom'];
		$_SESSION['dateto'] = $_POST['dateto'];	
		
		if(($_SESSION['datefrom']) > ($_SESSION['dateto']))
		{
			Flash::addMessage("Wprowadzono błędny okres. Data ".$_SESSION['datefrom']." jest większa od ".$_SESSION['dateto'], Flash::WARNING);
		}
		
		View::renderTemplate('Menu/showBalance.html');
	}
	
	public function balanceAction()
	{
		if( isset($_SESSION['datefrom']))
		{
			View::renderTemplate('Menu/showBalance.html');
		}
		else
		{
			Flash::addMessage("Wybierz okres, dla którego chcesz sprawdzić bilans w zakładce \"Przeglądaj bilans\"", Flash::WARNING);
			View::renderTemplate('Menu/menu.html');
		}
	}
	*/
}