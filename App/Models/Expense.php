<?php

namespace App\Models;

use PDO;
use \Core\View;
use \App\Flash;



class Expense extends \Core\Model
{
    public $amountErrors = [];
    public $dateErrors = [];

    public function __construct( $data = [] ) 
    {
        foreach ( $data as $key => $value ) 
        {
            $this->$key = $value;
        }
    }


    public static function getUserExpenseCategories($id) 
    {
        $db = static::getDB();

        $query_expense_categories=$db->prepare("SELECT id, name FROM expenses_category_assigned_to_users WHERE user_id=:id");

        $query_expense_categories->bindValue(':id',$_SESSION['user_id'],PDO::PARAM_INT);
        $query_expense_categories->execute();
        $expense_categories=$query_expense_categories->fetchAll();

        return $expense_categories; 
    }



   
    public static function getUserPaymentMethods($id) 
    {
        $db = static::getDB();

        $query_payment_methods=$db->prepare("SELECT id, name FROM payment_methods_assigned_to_users WHERE user_id=:id");

        $query_payment_methods->bindValue(':id',$_SESSION['user_id'],PDO::PARAM_INT);
        $query_payment_methods->execute();
        $payment_methods=$query_payment_methods->fetchAll();

        return $payment_methods; 
    }

    

    public function getUserCategoryId($user_id) 
    {
        $sql = 'SELECT id, user_id, name FROM expenses_category_assigned_to_users WHERE user_id = :user_id AND name = :name';

        $db = static::getDB();

        $stmt = $db->prepare($sql);
        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $this->expense_category, PDO::PARAM_STR );
        $stmt->execute();

        $categories = $stmt -> fetch();

        return $categories['id'];
    }


     public function getUserPaymentMethodId($user_id) 
    {
        $sql = 'SELECT id, user_id, name FROM payment_methods_assigned_to_users WHERE user_id = :user_id AND name = :name';

        $db = static::getDB();

        $stmt = $db->prepare($sql);
        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $this->payment_method, PDO::PARAM_STR );
        $stmt->execute();

        $payment_methods = $stmt -> fetch();

        return $payment_methods['id'];
    }

    

     public function saveUserExpense($user_id) 
    {
        $this->expense_amount = str_replace( [','], ['.'], $this->expense_amount);
        $this->amountErrors = Flash::validateAmount( $this->expense_amount);
        $this->dateErrors = Flash::validateDate( $this->date );

        if ( ( empty( $this->amountErrors ) ) && ( empty( $this->dateErrors ) ) ) 
        {
            $sql = 'INSERT INTO expenses VALUES(NULL, :user_id, :expense_category_assigned_to_user_id, :payment_method_assigned_to_user_id, :amount, :date_of_expense, :expense_note)';

            $db = static::getDB();
            $stmt = $db->prepare( $sql );
            $expense_note = filter_var( $this->expense_note, FILTER_SANITIZE_SPECIAL_CHARS);

            $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
            $stmt->bindValue( ':expense_category_assigned_to_user_id', $this->getUserCategoryId($user_id), PDO::PARAM_INT );
            $stmt->bindValue( ':payment_method_assigned_to_user_id', $this->getUserPaymentMethodId($user_id), PDO::PARAM_INT );
            $stmt->bindValue( ':amount', $this->expense_amount, PDO::PARAM_STR );
            $stmt->bindValue( ':date_of_expense', $this->date, PDO::PARAM_STR );
            $stmt->bindValue( ':expense_note', $expense_note, PDO::PARAM_STR );

            return $stmt->execute();
        }
        return false;
    }

    public static function checkExpenseCategoryExists($user_id, $oldExpenseCategoryName) 
    {
        

        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT * FROM expenses_category_assigned_to_users WHERE user_id = :user_id AND name =:name' );

        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $oldExpenseCategoryName, PDO::PARAM_STR );
        $stmt->setFetchMode( PDO::FETCH_ASSOC );
        $stmt->execute();

        return $stmt->fetchAll();
    }



    public static function addNewExpenseCategory( $user_id, $newExpenseCategoryName)
    {
        $db = static::getDB();
        
        $stmt = $db->prepare('INSERT INTO expenses_category_assigned_to_users VALUES (NULL, :user_id, :name)');
        
        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $newExpenseCategoryName, PDO::PARAM_STR );        
        
        return $stmt->execute();
    }

    public static function getEditedExpenseCategoryId( $user_id, $expenseCategoryName)
    {

        $db = static::getDB();
        
        $stmt = $db->prepare( 'SELECT id FROM expenses_category_assigned_to_users WHERE name =:name AND user_id =:user_id ' );

        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $expenseCategoryName, PDO::PARAM_STR );
        
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public static function editExpenseCategory( $user_id, $oldExpenseCategoryName, $newExpenseCategoryName )
    {
        $categoryId = static::getEditedExpenseCategoryId($user_id, $oldExpenseCategoryName);
        
        $db = static::getDB();
        
        $stmt = $db->prepare( 'UPDATE expenses_category_assigned_to_users SET name = :name WHERE id = :id ' );

        $stmt->bindValue( ':id', $categoryId, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $newExpenseCategoryName, PDO::PARAM_STR );        
        
        return $stmt->execute();    
    }

    public static function checkExpenseCategoryRecordsExists($user_id, $deletedExpensesCategoryName) 
    {
        
        $deletedExpensesCategoryId = static::getEditedExpenseCategoryId($user_id, $deletedExpensesCategoryName);
        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT * FROM expenses WHERE user_id = :user_id AND expense_category_assigned_to_user_id =:category_id' );

        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':category_id', $deletedExpensesCategoryId, PDO::PARAM_STR );
        $stmt->setFetchMode( PDO::FETCH_ASSOC );
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function deleteExpensesCategory($user_id, $deletedExpensesCategoryName) 
    {
                    
        $db = static::getDB();
        
        $stmt = $db->prepare('DELETE FROM expenses_category_assigned_to_users WHERE user_id = :user_id AND name =:name');
        
        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $deletedExpensesCategoryName, PDO::PARAM_STR );        
        
        return $stmt->execute();
    }

    public static function deleteExpensesFromUserExpenseCategory($user_id, $deletedExpensesCategoryName) 
    {
       
        $deletedExpensesCategoryId = static::getEditedExpenseCategoryId($user_id, $deletedExpensesCategoryName); 
        
        $db = static::getDB();
        
        $stmt = $db->prepare('DELETE FROM expenses WHERE user_id = :user_id AND expense_category_assigned_to_user_id =:deletedExpensesCategoryName');
        
        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':deletedExpensesCategoryName', $deletedExpensesCategoryName, PDO::PARAM_INT );  
        
        return $stmt->execute();
    }

    public static function getUserExpensesFromCategory ($user_id, $deletedExpensesCategoryId) 
    {
        $db = static::getDB();
        
        $stmt = $db->prepare( 'SELECT * FROM expenses WHERE user_id = :user_id AND expense_category_assigned_to_user_id =:deletedExpensesCategoryId' );
        
        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':deletedExpensesCategoryId', $deletedExpensesCategoryId, PDO::PARAM_INT ); 
        
        $stmt->setFetchMode( PDO::FETCH_ASSOC );
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public static function moveExpensesToDifferentCategory($user_id, $deletedExpensesCategoryName, $targetedExpensesCategoryName ) 
    {
        
        $deletedExpensesCategoryId = static::getEditedExpenseCategoryId($user_id, $deletedExpensesCategoryName);
        $targetedExpensesCategoryId = static::getEditedExpenseCategoryId($user_id, $targetedExpensesCategoryName);
        $userExpenses = static::getUserExpensesFromCategory($user_id, $deletedExpensesCategoryId);
                
        $db = static::getDB();
        
        foreach ($userExpenses as $expense)
        {
            
            $stmt = $db->prepare('INSERT INTO expenses VALUES(NULL, :user_id, :expense_category_assigned_to_user_id, :payment_method_assigned_to_user_id, :amount, :date_of_expense, :expense_comment)');
            
            $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
            $stmt->bindValue( ':expense_category_assigned_to_user_id', $targetedExpensesCategoryId, PDO::PARAM_INT );  
            $stmt->bindValue( ':payment_method_assigned_to_user_id', $expense['payment_method_assigned_to_user_id'], PDO::PARAM_INT );          
            $stmt->bindValue( ':amount', $expense['amount'], PDO::PARAM_STR );
            $stmt->bindValue( ':date_of_expense', $expense['date_of_expense'], PDO::PARAM_STR );
            $stmt->bindValue( ':expense_comment', $expense['expense_comment'], PDO::PARAM_STR );
            
            $stmt->execute();
        }
        return true;
    }

    public static function checkPaymentMethodExists($user_id, $oldPaymentMethod) 
    {
        
        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT * FROM payment_methods_assigned_to_users WHERE user_id = :user_id AND name =:name' );

        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $oldPaymentMethod, PDO::PARAM_STR );
        $stmt->setFetchMode( PDO::FETCH_ASSOC );
        $stmt->execute();

        return $stmt->fetchAll();
    }

     public static function addNewPaymentMethod( $user_id, $newPaymentMethod)
    {
        $db = static::getDB();
        
        $stmt = $db->prepare('INSERT INTO payment_methods_assigned_to_users VALUES (NULL, :user_id, :name)');
        
        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $newPaymentMethod, PDO::PARAM_STR );        
        
        return $stmt->execute();
    }

    public static function getEditedPaymentMethodId( $user_id, $paymentMethod)
    {

        $db = static::getDB();
        
        $stmt = $db->prepare( 'SELECT id FROM payment_methods_assigned_to_users WHERE name =:name AND user_id =:user_id ' );

        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $paymentMethod, PDO::PARAM_STR );
        
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public static function editPaymentMethod( $user_id, $oldPaymentMethod, $newPaymentMethod )
    {
        $categoryId = static::getEditedPaymentMethodId($user_id, $oldPaymentMethod);
        
        $db = static::getDB();
        
        $stmt = $db->prepare( 'UPDATE payment_methods_assigned_to_users SET name = :name WHERE id = :id ' );

        $stmt->bindValue( ':id', $categoryId, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $newPaymentMethod, PDO::PARAM_STR );        
        
        return $stmt->execute();    
    }

    public static function deletePaymentMethod($user_id, $deletedPaymentMethodName) 
    {
                    
        $db = static::getDB();
        
        $stmt = $db->prepare('DELETE FROM payment_methods_assigned_to_users WHERE user_id = :user_id AND name =:name');
        
        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $deletedPaymentMethodName, PDO::PARAM_STR );        
        
        return $stmt->execute();
    }

    public static function deletePaymentsFromUserPaymentCategory($user_id, $deletedPaymentMethodName) 
    {
       
        $deletedPaymentMethodId = static::getEditedPaymentMethodId($user_id, $deletedPaymentMethodName); 
        
        $db = static::getDB();
        
        $stmt = $db->prepare('DELETE FROM expenses WHERE user_id = :user_id AND payment_method_assigned_to_user_id =:deletedPaymentMethodId');
        
        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':deletedPaymentMethodId', $deletedPaymentMethodId, PDO::PARAM_INT );  
        
        return $stmt->execute();
    }

    public static function getUserPaymentsFromCategory ($user_id, $deletedPaymentMethodId) 
    {
        $db = static::getDB();
        
        $stmt = $db->prepare( 'SELECT * FROM expenses WHERE user_id = :user_id AND payment_method_assigned_to_user_id =:deletedPaymentMethodId' );
        
        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':deletedPaymentMethodId', $deletedPaymentMethodId, PDO::PARAM_INT ); 
        
        $stmt->setFetchMode( PDO::FETCH_ASSOC );
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public static function checkPaymentMethodRecordsExists($user_id, $deletedPaymentMethodName) 
    {
        
        $deletedPaymentMethodId = static::getEditedPaymentMethodId($user_id, $deletedPaymentMethodName);
        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT * FROM expenses WHERE user_id = :user_id AND payment_method_assigned_to_user_id =:category_id' );

        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':category_id', $deletedPaymentMethodId, PDO::PARAM_STR );
        $stmt->setFetchMode( PDO::FETCH_ASSOC );
        $stmt->execute();

        return $stmt->fetchAll();
    }

    
    public static function movePaymentsToDifferentCategory($user_id, $deletedPaymentMethodName, $targetedPaymentMethodName ) 
    {
        
        $deletedPaymentMethodId = static::getEditedPaymentMethodId($user_id, $deletedPaymentMethodName);
        $targetedPaymentMethodId = static::getEditedPaymentMethodId($user_id, $targetedPaymentMethodName);
        $userPayments = static::getUserPaymentsFromCategory($user_id, $deletedPaymentMethodId);
                
        $db = static::getDB();
        
        foreach ($userPayments as $payment)
        {
            $stmt = $db->prepare('INSERT INTO expenses VALUES(NULL, :user_id, :expense_category_assigned_to_user_id, :payment_method_assigned_to_user_id, :amount, :date_of_expense, :expense_comment)');
            
            $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
            $stmt->bindValue( ':expense_category_assigned_to_user_id', $payment['expense_category_assigned_to_user_id'], PDO::PARAM_INT );
            $stmt->bindValue( ':payment_method_assigned_to_user_id', $targetedPaymentMethodId, PDO::PARAM_INT );             
            $stmt->bindValue( ':amount', $payment['amount'], PDO::PARAM_STR );
            $stmt->bindValue( ':date_of_expense', $payment['date_of_expense'], PDO::PARAM_STR );
            $stmt->bindValue( ':expense_comment', $payment['expense_comment'], PDO::PARAM_STR );
            
            $stmt->execute();            
        }
        return true;
    }

    public static function editSingleExpense($user_id, $expense_id, $expense_comment, $expense_amount, $date_of_expense, $expense_category, $payment_category) 
    {
        $db = static::getDB();

        $expense_amount = str_replace( [','], ['.'], $expense_amount );
        $expense_category_id = static::getUserExpenseCategoryId($expense_category, $user_id) ;
        $payment_category_id = static::getUserPaymentCategoryId($payment_category, $user_id) ;

        $stmt = $db->prepare( 'UPDATE expenses SET amount = :expense_amount, date_of_expense =:date_of_expense, expense_comment = :expense_comment, expense_category_assigned_to_user_id = :expense_category_id, payment_method_assigned_to_user_id = :payment_category_id WHERE id = :expense_id AND user_id = :user_id ' );

        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':expense_id', $expense_id, PDO::PARAM_INT );
        $stmt->bindValue( ':expense_category_id', $expense_category_id, PDO::PARAM_INT );
        $stmt->bindValue( ':payment_category_id', $payment_category_id, PDO::PARAM_INT );
        $stmt->bindValue( ':date_of_expense', $date_of_expense, PDO::PARAM_STR );
        $stmt->bindValue( ':expense_amount', $expense_amount, PDO::PARAM_STR );
        $stmt->bindValue( ':expense_comment', $expense_comment, PDO::PARAM_STR);       


        return $stmt->execute();
    }

    public static function getUserExpenseCategoryId($expense_category, $user_id) 
    {
        $sql = 'SELECT id, user_id, name FROM expenses_category_assigned_to_users WHERE user_id = :user_id AND name = :name';

        $db = static::getDB();

        $stmt = $db->prepare($sql);
        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $expense_category, PDO::PARAM_STR );
        $stmt->execute();

        $categories = $stmt -> fetch();

        return $categories['id'];
    }

    public static function getUserPaymentCategoryId($payment_category, $user_id) 
    {
        $sql = 'SELECT id, user_id, name FROM payment_methods_assigned_to_users WHERE user_id = :user_id AND name = :name';

        $db = static::getDB();

        $stmt = $db->prepare($sql);
        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $payment_category, PDO::PARAM_STR );
        $stmt->execute();

        $categories = $stmt -> fetch();

        return $categories['id'];
    }

     public static function getSingleExpenseCategoryId ($user_id, $expense_id, $categoryToMove)
    {
        $categoryId =                      
        
        $db = static::getDB();        
        
            
        $stmt = $db->prepare('UPDATE expenses SET userExpenseCategoryId =:userExpenseCategoryId WHERE id = :id');
        
        $stmt->bindValue( ':id', $expenseId, PDO::PARAM_INT );
        $stmt->bindValue( ':userExpenseCategoryId', $categoryId, PDO::PARAM_INT );             
        
    
        return $stmt->execute();
    }

     public static function deleteSingleExpense ($user_id, $expense_id) 
    {
        $db = static::getDB(); 
        
        $stmt = $db->prepare('DELETE FROM expenses WHERE user_id = :user_id AND id = :expense_id ' );

        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':expense_id', $expense_id, PDO::PARAM_INT );

        return $stmt->execute();
    } 


}