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

    public function newAction($arg1='', $arg2='',$arg3='')
    {
        $success = false;
        

        $userIncomeCategories = Income::getUserIncomeCategories($this->user->id);
        $userExpenseCategories = Expense::getUserExpenseCategories( $this->user->id);
        $userPaymentMethods = Expense::getUserPaymentMethods($this->user->id);
        

            if(empty($arg3))
            {
                View::renderTemplate('Settings/settings.html', 
                [
                    'user' => $this->user,
                    'userIncomeCategories' => $userIncomeCategories,                    
                    'userExpenseCategories' => $userExpenseCategories,
                    'userPaymentMethods' => $userPaymentMethods,
                    'pass' => $arg1,
                    'error' => $arg2,

                ] );
            }
            else
            {
                

                View::renderTemplate('Settings/settings.html', 
                [
                    'user' => $this->user,
                    'userIncomeCategories' => $userIncomeCategories,
                    'userExpenseCategories' => $userExpenseCategories,
                    'userPaymentMethods' => $userPaymentMethods,
                    'pass' => $arg1,
                    'error' => $arg2,
                    'active' => $arg3 
                    
                ] );
                $arg3='';

            }           
    }
        
    
        
    

    public function addIncomeCategoryAction()
    {
        
        $newIncomeCategory = $_POST['newIncomeCategory'] ;
        $active ='';

        if ( Income::checkIncomeCategoryExists($this->user->id, $newIncomeCategory ) ) 
        {
            
            $pass = '';
            $error = "Podana kategoria ju?? istnieje!";
            $active ='1';
            $this -> newAction( $pass, $error, $active);                              
        }

        else
        {
            Income::addNewIncomeCategory($this->user->id, $newIncomeCategory);
            $pass = "Dodano now?? kategori??!";
            $error = '';
            $active ='1';
            $this -> newAction( $pass, $error, $active);
        }     
    }

    public function editIncomeCategoryAction()
    {
        $editedIncomeCategory = $_POST['editedIncomeCategory'];
        $oldIncomeCategoryName = $_POST['incomeCategory'];
        $active ='';
        if ( Income::checkIncomeCategoryExists($this->user->id,$editedIncomeCategory))
        {            
            $pass = '';
            $error = "Podana kategoria ju?? istnieje!";
            $active ='2';
            $this -> newAction( $pass, $error, $active);            
        }

        else
        {
            Income::editIncomeCategory($this->user->id, $oldIncomeCategoryName, $editedIncomeCategory);
            $pass = "Pomy??lnie zmieniono nazw?? kategorii!";
            $error = '';
            $active ='2';
            $this -> newAction( $pass, $error, $active); 
        }     

    }

    public function chooseDeleteOptionAction()
    {

        $delete_option =  $_POST['delete_option'];
        if ($delete_option=='1')
        {
            $this->redirect( '/Settings/deleteIncomesCategory');
        }
        else if ($delete_option=='2')
        {
            $this->redirect( '/Settings/moveIncomeCategoryRecords');
        }
        else if ($delete_option=='3')
        {
            $this->redirect( '/Settings/deleteExpensesCategory');
        }
        else if ($delete_option=='4')
        {
            $this->redirect( '/Settings/moveExpenseCategoryRecords');
        }
        else if ($delete_option=='5')
        {
            $this->redirect( '/Settings/deleteExpensesCategory');
        }
        else if ($delete_option=='6')
        {
            $this->redirect( '/Settings/moveExpenseCategoryRecords');
        }
    }

    public function deleteIncomesCategoryAction()
    {
        $deletedIncomeCategory = $_POST['deletedIncomeCategory'];
        Income::deleteIncomesFromUserIncomeCategory( $this->user->id, $deletedIncomeCategory );
        Income::deleteIncomesCategory( $this->user->id, $deletedIncomeCategory );

        $pass = "Kategoria zosta??a usuni??ta";
        $error = '';
        $active ='3';
        $this -> newAction( $pass, $error, $active);
    }

    public function moveIncomesToDifferentCategoryAction()
    {
        $deletedIncomeCategory = $_POST['deletedIncomeCategory'];
        $targetedIncomeCategory = $_POST['targetedIncomeCategory'];
        Income::moveIncomesToDifferentCategory($this->user->id, $deletedIncomeCategory, $targetedIncomeCategory);
        Income::deleteIncomesFromUserIncomeCategory( $this->user->id, $deletedIncomeCategory );
        Income::deleteIncomesCategory( $this->user->id, $deletedIncomeCategory );
        $pass = "Kategoria zosta??a usuni??ta";
        $error = "Usuni??ta kategoria nie zawiera??a rekord??w"; 
        $active ='3';
        $this -> newAction( $pass, $error, $active); 
    }
    


    public function addExpenseCategoryAction()
    {
        $newExpenseCategory = $_POST['newExpenseCategory'] ;
        $active ='';

        if ( Expense::checkExpenseCategoryExists($this->user->id, $newExpenseCategory ) ) 
        {
            $pass = '';
            $error = "Podana kategoria ju?? istnieje!";
            $active ='4';
            $this -> newAction( $pass, $error, $active);           
             
        }

        else
        {
            Expense::addNewExpenseCategory($this->user->id, $newExpenseCategory);
            $pass = "Dodano now?? kategori??!";
            $error = '';
            $active ='4';
            $this -> newAction( $pass, $error, $active); 
        }     
    }

    public function editExpenseCategoryAction()
    {
        $editedExpenseCategory = $_POST['editedExpenseCategory'];
        $oldExpenseCategoryName = $_POST['oldExpenseCategoryName'];
        $active ='';

        if ( Expense::checkExpenseCategoryExists($this->user->id,$editedExpenseCategory))
        {
            $pass = '';
            $error = "Podana kategoria ju?? istnieje!";            
            $active ='5';
            $this -> newAction( $pass, $error, $active);
        }

        else
        {
            Expense::editExpenseCategory($this->user->id, $oldExpenseCategoryName, $editedExpenseCategory);
            $pass = "Pomy??lnie zmieniono nazw?? kategorii";
            $error = '';            
            $active ='5';
            $this -> newAction( $pass, $error, $active);
        }     

    }

    public function deleteExpensesCategoryAction()
    {
        $deletedExpenseCategory = $_POST['deletedExpenseCategory'];
        Expense::deleteExpensesFromUserExpenseCategory( $this->user->id, $deletedExpenseCategory );
        Expense::deleteExpensesCategory( $this->user->id, $deletedExpenseCategory );

        $pass = "Kategoria zosta??a usuni??ta";
        $error = ''; 
        $active ='6';
        $this -> newAction( $pass, $error, $active); 
    }

    public function moveExpensesToDifferentCategoryAction()
    {

        $deletedExpenseCategory = $_POST['deletedExpenseCategory'];

        $targetedExpenseCategory = $_POST['targetedExpenseCategory'];

        
        if (Expense::checkExpenseCategoryRecordsExists($this->user->id, $deletedExpenseCategory))
        {
	        Expense::moveExpensesToDifferentCategory($this->user->id, $deletedExpenseCategory, $targetedExpenseCategory);
	        Expense::deleteExpensesFromUserExpenseCategory( $this->user->id, $deletedExpenseCategory );
	        Expense::deleteExpensesCategory( $this->user->id, $deletedExpenseCategory );
	        $pass = "Kategoria zosta??a usuni??ta";
	        $error = ''; 
	        $active ='6';
	        $this -> newAction( $pass, $error, $active);
        }
        else
        {
        	Expense::deleteExpensesCategory( $this->user->id, $deletedExpenseCategory );
        	$pass = "Kategoria zosta??a usuni??ta";
	        $error = "Usuni??ta kategoria nie zawiera??a rekord??w";  
	        $active ='6';
	        $this -> newAction( $pass, $error, $active);
        }
         
    }

    public function addPaymentMethodAction()
    {
        $newPaymentMethod = $_POST['newPaymentMethod'] ;
        $active ='';

        if ( Expense::checkPaymentMethodExists($this->user->id, $newPaymentMethod ) ) 
        {
            $pass = '';
            $error = "Podana metoda p??atno??ci ju?? istnieje!";            
            $active ='7';
            $this -> newAction( $pass, $error, $active);
        }

        else
        {
            Expense::addNewPaymentMethod($this->user->id, $newPaymentMethod);  
            $pass = 'Dodano now?? metod?? p??atno??ci!';
            $error = '';            
            $active ='7';
            $this -> newAction( $pass, $error, $active);
        }     
    }

    public function editPaymentMethodAction()
    {
        $editedPaymentMethod = $_POST['editedPaymentMethod'];
        $oldPaymentMethodName = $_POST['oldPaymentMethodName'];
        $active ='';

        if ( Expense::checkPaymentMethodExists($this->user->id,$editedPaymentMethod))
        {
            $pass = '';
            $error = "Podana metoda p??atno??ci ju?? istnieje!";            
            $active ='8';
            $this -> newAction( $pass, $error, $active);
        }

        else
        {
            Expense::editPaymentMethod($this->user->id, $oldPaymentMethodName, $editedPaymentMethod);
            $pass = "Pomy??lnie zmieniono metod?? p??atno??ci";
            $error = '';            
            $active ='8';
            $this -> newAction( $pass, $error, $active);
        }     

    }

    public function deletePaymentMethodAction()
    {
        $deletedPaymentMethod = $_POST['deletedPaymentMethod'];
        Expense::deletePaymentsFromUserPaymentCategory( $this->user->id, $deletedPaymentMethod );
        Expense::deletePaymentMethod( $this->user->id, $deletedPaymentMethod );

        $pass = "Metoda p??atno??ci zosta??a usuni??ta";
        $error = ''; 
        $active ='9';
        $this -> newAction( $pass, $error, $active); 
    }

    public function movePaymentsToDifferentCategoryAction()
    {
        $deletedPaymentMethod = $_POST['deletedPaymentMethod'];
        $targetedPaymentMethod = $_POST['targetedPaymentMethod'];

        if (Expense::checkPaymentMethodRecordsExists($this->user->id, $deletedPaymentMethod))
        {
	        Expense::movePaymentsToDifferentCategory($this->user->id, $deletedPaymentMethod, $targetedPaymentMethod);
        	Expense::deletePaymentsFromUserPaymentCategory( $this->user->id, $deletedPaymentMethod );
        	Expense::deletePaymentMethod( $this->user->id, $deletedPaymentMethod );
	        $pass = "Metoda p??atno??ci zosta??a usuni??ta";
	        $error = ''; 
	        $active ='9';
	        $this -> newAction( $pass, $error, $active);
        }
        else
        {
        	Expense::deletePaymentMethod( $this->user->id, $deletedPaymentMethod );
        	$pass = "Metoda p??atno??ci zosta??a usuni??ta.";
	        $error = 'Metoda p??atno??ci nie zawiera??a ??adnych rekord??w.'; 
	        $active ='9';
	        $this -> newAction( $pass, $error, $active);
        }
    }              
}