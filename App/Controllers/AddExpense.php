<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Expense;
use \App\Models\DateManager;
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
            'success' => $success,
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

    public function getExpenseCategoryLimitAction() 
    { 
        $expense_category = $_GET['expense_category'];
        $expense_date = $_GET['expense_date'];
        $expense_amount = $_GET['expense_amount'];
        $user_id = $this->user->id;
        $expense_limit = Expense::getExpensesCategoryLimit($user_id, $expense_category);
        $expenses_sum = static::getExpensesSumForLimit($expense_date, $expense_category);
        $expense_limit_diff = $expense_limit - $expenses_sum - $expense_amount;
        if($expense_limit_diff < 0)
        {
            $expense_alert = "Uważaj przekroczysz limit o ".-($expense_limit_diff)." zł dla kategorii: ".$expense_category."!";                   
        }
        else
        {
            $expense_alert = "Pozostało ".$expense_limit_diff." zł dla kategorii: ".$expense_category;  
        }

        echo json_encode($expense_alert, JSON_UNESCAPED_UNICODE);
          
    }

    public function getExpensesSumForLimit($date, $expense_category)
    {
        $user_id = $this->user->id; 
        $year = date("Y",strtotime($date));
        $month = date("m",strtotime($date));
        $day = date("d",strtotime($date));

        $expense_date = DateManager::getFirstSecondDate($year,$month,$day);        
        $expenses_sum = Expense::getExpensesSumForLimit($expense_date, $expense_category, $user_id);
        return $expenses_sum;   
    }

    
}