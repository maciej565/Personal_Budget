<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Balance;
use \App\Flash;


class showBalance extends Authenticated
{
    protected function before() 
    {
        parent::before();
        $this->user = Auth::getUser();
    }


    public function selectPeriod()
    {
        $option_number =  $_POST['option_number'];
        if ($option_number=='1')
        {
            $this->redirect( '/showBalance/currentMonth');
        }
        else if ($option_number=='2')
        {
            $this->redirect( '/showBalance/lastMonth');
        }
        else if ($option_number=='3')
        {
            $this->redirect( '/showBalance/currentYear');
        }
    }
/*----------------------------------------------------------------------------------*/
    public function currentMonthAction()
    {
        $success = false; 
        $date = Balance::getCurrentMonthDate();        
        $incomeBalanceTable = Balance::getIncomes( $date,$this->user->id);
        $expenseBalanceTable = Balance::getExpenses( $date,$this->user->id);
        $balance = Balance::getBalance($date,$this->user->id);
        $percentageIncome = Balance::percentageIncome($date,$this->user->id);
        $percentageExpense = Balance::percentageExpense($date,$this->user->id);
        $incomeDataPoints = Balance::getIncomes($date,$this->user->id);
        $expenseDataPoints = Balance::getExpenses($date,$this->user->id);  

        View::renderTemplate('Mainpage/balance.html', 
        [
            'user' => $this->user,
            'incomeBalanceTable' => $incomeBalanceTable,
            'expenseBalanceTable' => $expenseBalanceTable,
            'balance' => $balance,
            'percentageIncome' => $percentageIncome,
            'percentageExpense' => $percentageExpense,
            'incomeDataPoints' => $incomeDataPoints,
            'expenseDataPoints' => $expenseDataPoints,          
            'first_date' => $date['first_date'],
            'second_date' => $date['second_date'],
            'balancePeriod' => "Bieżący miesiąc",
            'balanceTitle' => "z bieżącego miesiąca"
            
        ] );
    }

    public function lastMonthAction()
    {
        $success = false; 
        $date = Balance::getlastMonthDate();        
        $incomeBalanceTable = Balance::getIncomes( $date,$this->user->id);
        $expenseBalanceTable = Balance::getExpenses( $date,$this->user->id);
        $balance = Balance::getBalance($date,$this->user->id);
        $percentageIncome = Balance::percentageIncome($date,$this->user->id);
        $percentageExpense = Balance::percentageExpense($date,$this->user->id);
        $incomeDataPoints = Balance::getIncomes($date,$this->user->id);
        $expenseDataPoints = Balance::getExpenses($date,$this->user->id); 
        
        View::renderTemplate('Mainpage/balance.html', 
        [
            'user' => $this->user,
            'incomeBalanceTable' => $incomeBalanceTable,
            'expenseBalanceTable' => $expenseBalanceTable,
            'balance' => $balance,
            'percentageIncome' => $percentageIncome,
            'percentageExpense' => $percentageExpense,
            'incomeDataPoints' => $incomeDataPoints,
            'expenseDataPoints' => $expenseDataPoints, 
            'first_date' => $date['first_date'],
            'second_date' => $date['second_date'],
            'balancePeriod' => "Ostatni miesiąc",
            'balanceTitle' => "z ostatniego miesiąca"
            
        ] );
    }

    public function currentYearAction()
    {
        $success = false; 
        $date = Balance::getCurrentYearDate();        
        $incomeBalanceTable = Balance::getIncomes($date,$this->user->id);
        $expenseBalanceTable = Balance::getExpenses( $date,$this->user->id);
        $balance = Balance::getBalance($date,$this->user->id);
        $percentageIncome = Balance::percentageIncome($date,$this->user->id);
        $percentageExpense = Balance::percentageExpense($date,$this->user->id);
        $incomeDataPoints = Balance::getIncomes($date,$this->user->id);
        $expenseDataPoints = Balance::getExpenses($date,$this->user->id); 

        View::renderTemplate('Mainpage/balance.html', 
        [
            'user' => $this->user,
            'incomeBalanceTable' => $incomeBalanceTable,
            'expenseBalanceTable' => $expenseBalanceTable,
            'balance' => $balance,
            'percentageIncome' => $percentageIncome,
            'percentageExpense' => $percentageExpense,
            'incomeDataPoints' => $incomeDataPoints,
            'expenseDataPoints' => $expenseDataPoints, 
            'first_date' => $date['first_date'],
            'second_date' => $date['second_date'],
            'balancePeriod' => "Bieżący rok",
            'balanceTitle' => "z bieżącego roku"
            
        ] );
    }

    public function selectedDateAction()
    {
        $success = false; 
        $date = Balance::getUserSelectedDate();        
        $incomeBalanceTable = Balance::getIncomes( $date,$this->user->id);
        $expenseBalanceTable = Balance::getExpenses( $date,$this->user->id);
        $balance = Balance::getBalance($date,$this->user->id);
        $percentageIncome = Balance::percentageIncome($date,$this->user->id);
        $percentageExpense = Balance::percentageExpense($date,$this->user->id);
        $incomeDataPoints = Balance::getIncomes($date,$this->user->id);
        $expenseDataPoints = Balance::getExpenses($date,$this->user->id); 
        
        View::renderTemplate('Mainpage/balance.html', 
        [
            'user' => $this->user,
            'incomeBalanceTable' => $incomeBalanceTable,
            'expenseBalanceTable' => $expenseBalanceTable,
            'balance' => $balance,
            'percentageIncome' => $percentageIncome,
            'percentageExpense' => $percentageExpense,
            'incomeDataPoints' => $incomeDataPoints,
            'expenseDataPoints' => $expenseDataPoints, 
            'first_date' => $date['first_date'],
            'second_date' => $date['second_date'],
            'balancePeriod' => "Własny przedział czasu",
            'balanceTitle' => "od ".$date['first_date']." do ".$date['second_date']
            
        ] );
    } 
}