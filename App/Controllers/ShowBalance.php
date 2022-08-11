<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Balance;
use \App\Models\Income;
use \App\Models\Expense;
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
    

    public function currentMonthAction($arg1='', $arg2='')
    {
        $success = false; 
        $date = Balance::getCurrentMonthDate();        
        $incomeBalanceTable = Balance::getIncomes($date,$this->user->id);
        $expenseBalanceTable = Balance::getExpenses( $date,$this->user->id);        
        $balance = Balance::getBalance($date,$this->user->id);
        $percentageIncome = Balance::percentageIncome($date,$this->user->id);
        $percentageExpense = Balance::percentageExpense($date,$this->user->id);
        $incomeDataPoints = Balance::getIncomes($date,$this->user->id);
        $expenseDataPoints = Balance::getExpenses($date,$this->user->id);
        $userIncomeCategories = Income::getUserIncomeCategories( $this->user->id); 
        $userExpenseCategories = Expense::getUserExpenseCategories( $this->user->id);
        $userPaymentMethods = Expense::getUserPaymentMethods( $this->user->id);
        

        View::renderTemplate('Mainpage/balance.html', 
        [
            'user' => $this->user,
            'incomeBalanceTable' => $incomeBalanceTable,
            'userIncomeCategories' => $userIncomeCategories,
            'userExpenseCategories' => $userExpenseCategories,
            'userPaymentMethods' => $userPaymentMethods,
            'expenseBalanceTable' => $expenseBalanceTable,            
            'balance' => $balance,
            'percentageIncome' => $percentageIncome,
            'percentageExpense' => $percentageExpense,
            'incomeDataPoints' => $incomeDataPoints,
            'expenseDataPoints' => $expenseDataPoints,          
            'first_date' => $date['first_date'],
            'second_date' => $date['second_date'],
            'balancePeriod' => "Bieżący miesiąc",
            'balanceTitle' => "z bieżącego miesiąca",
            'success' => $arg1,
            'error' => $arg2
        
        ] );
    }

    public function lastMonthAction($arg1='', $arg2='')
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
        $userIncomeCategories = Income::getUserIncomeCategories( $this->user->id);
        $userExpenseCategories = Expense::getUserExpenseCategories( $this->user->id);
        $userPaymentMethods = Expense::getUserPaymentMethods( $this->user->id);
        
        View::renderTemplate('Mainpage/balance.html', 
        [
            'user' => $this->user,
            'incomeBalanceTable' => $incomeBalanceTable,
            'userIncomeCategories' => $userIncomeCategories,
            'userExpenseCategories' => $userExpenseCategories,
            'userPaymentMethods' => $userPaymentMethods,
            'expenseBalanceTable' => $expenseBalanceTable,
            'balance' => $balance,
            'percentageIncome' => $percentageIncome,
            'percentageExpense' => $percentageExpense,
            'incomeDataPoints' => $incomeDataPoints,
            'expenseDataPoints' => $expenseDataPoints, 
            'first_date' => $date['first_date'],
            'second_date' => $date['second_date'],
            'balancePeriod' => "Ostatni miesiąc",
            'balanceTitle' => "z ostatniego miesiąca",
            'success' => $arg1,
            'error' => $arg2
            
        ] );
    }

    public function currentYearAction($arg1='', $arg2='')
    {
        $success = false; 
        $date = Balance::getCurrentYearDate();
        $userExpenseCategories = Expense::getUserExpenseCategories( $this->user->id);
        $userPaymentMethods = Expense::getUserPaymentMethods( $this->user->id);      
        $incomeBalanceTable = Balance::getIncomes($date,$this->user->id);
        $expenseBalanceTable = Balance::getExpenses( $date,$this->user->id);
        $balance = Balance::getBalance($date,$this->user->id);
        $percentageIncome = Balance::percentageIncome($date,$this->user->id);
        $percentageExpense = Balance::percentageExpense($date,$this->user->id);
        $incomeDataPoints = Balance::getIncomes($date,$this->user->id);
        $expenseDataPoints = Balance::getExpenses($date,$this->user->id);
        $userIncomeCategories = Income::getUserIncomeCategories( $this->user->id); 

        View::renderTemplate('Mainpage/balance.html', 
        [
            'user' => $this->user,
            'userExpenseCategories' => $userExpenseCategories,
            'userPaymentMethods' => $userPaymentMethods,
            'incomeBalanceTable' => $incomeBalanceTable,
            'userIncomeCategories' => $userIncomeCategories,
            'expenseBalanceTable' => $expenseBalanceTable,
            'balance' => $balance,
            'percentageIncome' => $percentageIncome,
            'percentageExpense' => $percentageExpense,
            'incomeDataPoints' => $incomeDataPoints,
            'expenseDataPoints' => $expenseDataPoints, 
            'first_date' => $date['first_date'],
            'second_date' => $date['second_date'],
            'balancePeriod' => "Bieżący rok",
            'balanceTitle' => "z bieżącego roku",
            'success' => $arg1,
            'error' => $arg2

            
        ] );
    }

    public function selectedDateAction($arg1='', $arg2='')
    {
                
        
        $success = false; 
        $date = Balance::getUserSelectedDate();
        $userExpenseCategories = Expense::getUserExpenseCategories( $this->user->id);
        $userPaymentMethods = Expense::getUserPaymentMethods( $this->user->id);       
        $incomeBalanceTable = Balance::getIncomes( $date,$this->user->id);
        $expenseBalanceTable = Balance::getExpenses( $date,$this->user->id);
        $balance = Balance::getBalance($date,$this->user->id);
        $percentageIncome = Balance::percentageIncome($date,$this->user->id);
        $percentageExpense = Balance::percentageExpense($date,$this->user->id);
        $incomeDataPoints = Balance::getIncomes($date,$this->user->id);
        $expenseDataPoints = Balance::getExpenses($date,$this->user->id); 
        $userIncomeCategories = Income::getUserIncomeCategories( $this->user->id);
       

        View::renderTemplate('Mainpage/balance.html', 
        [

            'user' => $this->user,            
            'userExpenseCategories' => $userExpenseCategories,
            'userPaymentMethods' => $userPaymentMethods,
            'incomeBalanceTable' => $incomeBalanceTable,
            'userIncomeCategories' => $userIncomeCategories,
            'expenseBalanceTable' => $expenseBalanceTable,
            'balance' => $balance,
            'percentageIncome' => $percentageIncome,
            'percentageExpense' => $percentageExpense,
            'incomeDataPoints' => $incomeDataPoints,
            'expenseDataPoints' => $expenseDataPoints, 
            'first_date' => $date['first_date'],
            'second_date' => $date['second_date'],
            'balancePeriod' => "Własny przedział czasu",
            'balanceTitle' => "od ".$date['first_date']." do ".$date['second_date'],
            'success' => $arg1,
            'error' => $arg2
            
        ] );

    }   

    


   

   
    protected function editSingleExpenseAction() 
    {
        
        $expense_id = $_POST['modal_expense_id'];        
        $expense_amount = $_POST['modal_expense_value'];
        $date_of_expense = $_POST['modal_date_of_expense'];
        $expense_category = $_POST['modal_expense_category'];
        
        $expense_comment = $_POST['modal_expense_comment'];
        $date = ['first_date' => $_POST['expense_first_date'],
        'second_date' => $_POST['expense_second_date']];
        $currentMonthDate = Balance::getCurrentMonthDate();
        $currentYearDate = Balance::getCurrentYearDate();
        $lastMonthdate = Balance::getLastMonthDate();


                
                if (Expense::editSingleExpense($this->user->id, $expense_id, $expense_comment, $expense_amount, $date_of_expense, $expense_category))
                {
                    if ($date === $currentMonthDate) 
                    {
                        $message = "Poprawnie zmieniono wydatek";
                        $error = '';
                        $this -> currentMonthAction($message, $error);
                    }
                    else if ($date === $currentYearDate) 
                    {
                        $message = "Poprawnie zmieniono wydatek";
                        $error = '';
                        $this -> currentYearAction($message, $error);
                    }
                    else if ($date === $lastMonthdate) 
                    {
                        $message = "Poprawnie zmieniono wydatek";
                        $error = '';
                        $this -> lastMonthAction($message, $error);
                    }
                }
                else
                {
                    $message = '';
                    $error = "Nie udało się przetworzyć zapytania";
                    $this -> currentMonthAction($message, $error);
                }

            
            
              

    }


    protected function editSingleIncomeAction() 
    {        
        $income_id = $_POST['modal_income_id'];        
        $income_amount = $_POST['modal_income_value'];
        $date_of_income = $_POST['modal_date_of_income'];
        $income_category = $_POST['modal_income_category'];
        
        $income_comment = $_POST['modal_income_comment'];
        $date = ['first_date' => $_POST['income_first_date'],
        'second_date' => $_POST['income_second_date']];
        $currentMonthDate = Balance::getCurrentMonthDate();
        $currentYearDate = Balance::getCurrentYearDate();
        $lastMonthdate = Balance::getLastMonthDate();        
               
        
        if (Income::editSingleIncome($this->user->id, $income_id, $income_comment, $income_amount, $date_of_income, $income_category))
        {
            if ($date === $currentMonthDate) 
            {
                $message = "Poprawnie zmieniono przychód";
                $error = '';
                $this -> currentMonthAction($message, $error);

            }
            else if ($date === $currentYearDate) 
            {
                $message = "Poprawnie zmieniono przychód";
                $error = '';
                $this -> currentYearAction($message, $error);
            }
            else if ($date === $lastMonthdate) 
            {
                $message = "Poprawnie zmieniono przychód";
                $error = '';
                $this -> lastMonthAction($message, $error);
            }
        }
        else
        {
            $message = '';
            $error = "Niestety nie udało się zmienić przychodu";
            $this -> currentMonthAction($message, $error);
        }          
    }

    protected function deleteSingleIncomeAction() 
    {        
        $income_id = $_POST['deleted_income_id'];      
        $date = ['first_date' => $_POST['income_first_date'],
        'second_date' => $_POST['income_second_date']];
        $currentMonthDate = Balance::getCurrentMonthDate();
        $currentYearDate = Balance::getCurrentYearDate();
        $lastMonthdate = Balance::getLastMonthDate();
        
        if (Income::deleteSingleIncome($this->user->id, $income_id))
        {
            if ($date === $currentMonthDate) 
            {
                $message = "Usunięto przychód!";
                $error = '';
                $this -> currentMonthAction($message, $error);

            }
            else if ($date === $currentYearDate) 
            {
                $message = "Usunięto przychód!";
                $error = '';
                $this -> currentYearAction($message, $error);
            }
            else if ($date === $lastMonthdate) 
            {
                $message = "Usunięto przychód";
                $error = '';
                $this -> lastMonthAction($message, $error);
            }
        }
        else
        {
            $message = '';
            $error = "Niestety nie udało się usunąć przychodu";
            $this -> currentMonthAction($message, $error);
        }           

    }
     protected function deleteSingleExpenseAction() 
    {
        
        $expense_id = $_POST['deleted_expense_id'];      
        $date = ['first_date' => $_POST['expense_first_date'],
        'second_date' => $_POST['expense_second_date']];
        $currentMonthDate = Balance::getCurrentMonthDate();
        $currentYearDate = Balance::getCurrentYearDate();
        $lastMonthdate = Balance::getLastMonthDate();


            
               
        
        if (Expense::deleteSingleExpense($this->user->id, $expense_id))

        {
            if ($date === $currentMonthDate) 
            {
                $message = "Usunięto wydatek!";
                $error = '';
                $this -> currentMonthAction($message, $error);

            }
            else if ($date === $currentYearDate) 
            {
                $message = "Usunięto wydatek!";
                $error = '';
                $this -> currentYearAction($message, $error);
            }
            else if ($date === $lastMonthdate) 
            {
                $message = "Usunięto wydatek";
                $error = '';
                $this -> lastMonthAction($message, $error);
            }
        }
        else
        {
            $message = '';
            $error = "Niestety nie udało się usunąć wydatku";
            $this -> currentMonthAction($message, $error);
        }             

    }

}