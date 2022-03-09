<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Income;
use \App\Models\Expense;
use \App\Flash;


class Settings extends Authenticated
{
    protected function before() 
    {
        parent::before();
        $this->user = Auth::getUser();
    }

    public function newAction($arg1='', $arg2='')
    {
        $success = false; 
        
        $userIncomeCategories = Income::getUserIncomeCategories($this->user->id);
        $userExpenseCategories = Expense::getUserExpenseCategories( $this->user->id);
        $userPaymentMethods = Expense::getUserPaymentMethods($this->user->id);
        

        View::renderTemplate('Settings/settings.html', 
        [
            'user' => $this->user,
            'userIncomeCategories' => $userIncomeCategories,
            'userExpenseCategories' => $userExpenseCategories,
            'userPaymentMethods' => $userPaymentMethods,
            'pass' => $arg1,
            'error' => $arg2
            
        ] );
    }

    public function addIncomeCategoryAction()
    {
        
        $newIncomeCategory = $_POST['newIncomeCategory'] ;

        if ( Income::checkIncomeCategoryExists($this->user->id, $newIncomeCategory ) ) 
        {
            $pass = '';
            $error = "Podana kategoria już istnieje!";            
            
            $this->redirect('/Settings/new#addIncome');
        }

        else
        {
            Income::addNewIncomeCategory($this->user->id, $newIncomeCategory);
            $pass = "Dodano nową kategorię!";
            $error = '';            
            
            $this->redirect('/Settings/new#addIncome');

        }     
    }

    public function editIncomeCategoryAction()
    {
        $editedIncomeCategory = $_POST['editedIncomeCategory'];
        $oldIncomeCategoryName = $_POST['incomeCategory'];
        if ( Income::checkIncomeCategoryExists($this->user->id,$editedIncomeCategory))
        {
            $pass = '';
            $error = "Podana kategoria już istnieje!";
            $this->redirect('/Settings/new#editIncome');            
           
        }

        else
        {
            Income::editIncomeCategory($this->user->id, $oldIncomeCategoryName, $editedIncomeCategory);
            $pass = "Zmieniono nazwę kategorii na";
            $error = '';            
            $this->redirect('/Settings/new#editIncome'); 
        }     

    }

    public function addExpenseCategoryAction()
    {
        $newExpenseCategory = $_POST['newExpenseCategory'] ;

        if ( Expense::checkExpenseCategoryExists($this->user->id, $newExpenseCategory ) ) 
        {
            $pass = '';
            $error = "Podana kategoria już istnieje!";            
            $this->redirect('/Settings/new#addExpense'); 
        }

        else
        {
            Expense::addNewExpenseCategory($this->user->id, $newExpenseCategory);
            $pass = "Dodano nową kategorię!";
            $error = '';            
            $this->redirect('/Settings/new#addExpense');
        }     
    }

    public function editExpenseCategoryAction()
    {
        $editedExpenseCategory = $_POST['editedExpenseCategory'];
        $oldExpenseCategoryName = $_POST['oldExpenseCategoryName'];
        if ( Expense::checkExpenseCategoryExists($this->user->id,$editedExpenseCategory))
        {
            $pass = '';
            $error = "Podana kategoria już istnieje!";            
            $this->redirect('/Settings/new#editExpense');
        }

        else
        {
            Expense::editExpenseCategory($this->user->id, $oldExpenseCategoryName, $editedExpenseCategory);
            $pass = "Zmieniono nazwę kategorii na";
            $error = '';            
            $this->redirect('/Settings/new#editExpense');
        }     

    }

    public function addPaymentMethodAction()
    {
        $newPaymentMethod = $_POST['newPaymentMethod'] ;

        if ( Expense::checkPaymentMethodExists($this->user->id, $newPaymentMethod ) ) 
        {
            $pass = '';
            $error = "Podana metoda płatności już istnieje!";            
            $this->redirect('/Settings/new#addPayment');
        }

        else
        {
            Expense::addNewPaymentMethod($this->user->id, $newPaymentMethod);  
            $pass = 'Dodano nową metodę płatności!';
            $error = '';            
            $this->redirect('/Settings/new#addPayment');
        }     
    }

    public function editPaymentMethodAction()
    {
        $editedPaymentMethod = $_POST['editedPaymentMethod'];
        $oldPaymentMethodName = $_POST['oldPaymentMethodName'];
        if ( Expense::checkPaymentMethodExists($this->user->id,$editedPaymentMethod))
        {
            $pass = '';
            $error = "Podana kategoria już istnieje!";            
            $this->redirect('/Settings/new#editPayment');
        }

        else
        {
            Expense::editPaymentMethod($this->user->id, $oldPaymentMethodName, $editedPaymentMethod);
            $pass = "Zmieniono nazwę płatności na";
            $error = '';            
            $this->redirect('/Settings/new#editPayment');
        }     

    }              
}