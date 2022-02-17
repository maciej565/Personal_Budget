<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Income;
use \App\Flash;


class addIncome extends Authenticated
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
        $userIncomeCategories = Income::getUserIncomeCategories( $this->user->id);
        

        View::renderTemplate('Mainpage/income.html', 
        [
            'user' => $this->user,
            'incomes' => $arg1,
            'errors' => $arg2,
            'userIncomeCategories' => $userIncomeCategories,
            'currentDate' => $currentDate
            
        ] );
    }

    public function createAction()
    {
        $income = new Income( $_POST );

        if ( $income->saveUserIncome( $this->user->id ) ) 
        {
            $success = true;
            $userIncomeCategories = Income::getUserIncomeCategories( $this->user->id );
            $currentDate = Flash::getCurrentDate();
            $category =  $_POST['category'];
            $amount =  $_POST['amount'];
            $date = $_POST['date'];

            View::renderTemplate('Mainpage/income.html', 
            [
                'user' => $this->user,
                'success' => $success,
                'userIncomeCategories' => $userIncomeCategories,
                'currentDate' => $currentDate,
                'category' => $category,
                'amount' => $amount,
                'date' => $date
            ] );

        } 
        else 
        {
            $incomes['amount'] = $_POST['amount'];
            $incomes['date'] = $_POST['date'];

            if ( isset( $_POST['comment'] ) ) 
            {
                $incomes['comment'] = $_POST['comment'];
            }
            
            $errors['dateError'] = $income -> dateErrors;
            $errors['amountError'] = $income -> amountErrors;

            $this -> newAction( $incomes, $errors);

        }
    }
}