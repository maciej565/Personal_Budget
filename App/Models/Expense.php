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

        $expense_categories = $stmt -> fetch();

        return $expense_categories['id'];
    }

     public function getUserPaymentId($user_id) 
    {
        $sql = 'SELECT id, user_id, name FROM payment_methods_assigned_to_users WHERE user_id = :user_id AND name = :name';

        $db = static::getDB();

        $stmt = $db->prepare($sql);
        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $this->payment_category, PDO::PARAM_STR );
        $stmt->execute();

        $payment_categories = $stmt -> fetch();

        return $payment_categories['id'];
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
            $stmt->bindValue( ':payment_method_assigned_to_user_id', $this->getUserPaymentId($user_id), PDO::PARAM_INT );
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
}