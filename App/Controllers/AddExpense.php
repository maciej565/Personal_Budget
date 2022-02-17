<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Expense;
use \App\Flash;


class addExpense extends Authenticated
{
    protected function before() 
    {
        parent::before();
        $this->user = Auth::getUser();
    }

    public function newAction($arg1 = 0, $arg2 = 0)
    {
        $success = false; 
        $currentDate = Flash::getCurrentDate();
        $userExpenseCategories = Expense::getUserExpenseCategories( $this->user->id);
        $userPaymentMethods = Expense::getUserPaymentMethods( $this->user->id);

        View::renderTemplate('Mainpage/expense.html', 
        [
            'user' => $this->user,
            'expenses' => $arg1,
            'errors' => $arg2,
            'userExpenseCategories' => $userExpenseCategories,
            'userPaymentMethods' => $userPaymentMethods,
            'currentDate' => $currentDate
            
        ] );
    }
    

    public function createAction()
    {
        $expense = new Expense($_POST);

        if ( $expense->saveUserExpense( $this->user->id ) ) 
        {
            $success = true;
            $userExpenseCategories = Expense::getUserExpenseCategories( $this->user->id );
            $userPaymentMethods = Expense::getUserPaymentMethods( $this->user->id );
            $currentDate = Flash::getCurrentDate();
            $expense_category =  $_POST['expense_category'];
            $expense_amount =  $_POST['expense_amount'];
            $date = $_POST['date'];

            View::renderTemplate('Mainpage/expense.html', 
            [
                'user' => $this->user,
                'success' => $success,
                'userExpenseCategories' => $userExpenseCategories,
                'userPaymentMethods' => $userPaymentMethods,
                'currentDate' => $currentDate,
                'expense_category' => $expense_category,
                'expense_amount' => $expense_amount,
                'date' => $date
            ] );

        } 
        else 
        {
            $expenses['amount'] = $_POST['expense_amount'];
            $expenses['date'] = $_POST['date'];

            if ( isset( $_POST['expense_note'] ) ) 
            {
                $expenses['note'] = $_POST['expense_note'];
            }
            
            $errors['dateError'] = $expense -> dateErrors;
            $errors['amountError'] = $expense -> amountErrors;

            $this -> newAction( $expenses, $errors);
        }
    }
}