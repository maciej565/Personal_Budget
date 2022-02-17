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

    /*

   

    

    public static function getUserExpenseCategoryId ( $user_id, $expenseCategoryName ) 
    {
        $db = static::getDB();
        
        $stmt = $db->prepare( 'SELECT id FROM expenses_category_assigned_to_users WHERE name =:name AND user_id =:user_id ' );

        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $expenseCategoryName, PDO::PARAM_STR );
        
        $stmt->execute();

        return $stmt->fetchColumn();
    }

   

    

    public static function getExpenses( $date, $user_id ) 
    {
        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT amount, date_of_expense, expense_category_assigned_to_user_id, expense, inc.name FROM incomes, incomes_category_assigned_to_users AS inc WHERE incomes.date_of_income BETWEEN :first_date AND :second_date AND incomes.user_id = :user_id AND incomes.income_category_assigned_to_user_id = inc.id ORDER BY incomes.date_of_income ASC' );

        $stmt->bindValue( ':first_date', $date['first_date'], PDO::PARAM_STR );
        $stmt->bindValue( ':second_date', $date['second_date'], PDO::PARAM_STR );
        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );

        $stmt->setFetchMode( PDO::FETCH_ASSOC );

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function getIncomesTotal( $date, $user_id ) 
    {
        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT ROUND(SUM(incomes.amount), 2), inc.name FROM incomes, incomes_category_assigned_to_users AS inc WHERE incomes.date_of_income BETWEEN :first_date AND :second_date AND incomes.user_id = :user_id AND incomes.income_category_assigned_to_user_id = inc.id GROUP BY incomes.income_category_assigned_to_user_id');

        $stmt->bindValue( ':first_date', $date['first_date'], PDO::PARAM_STR );
        $stmt->bindValue( ':second_date', $date['second_date'], PDO::PARAM_STR );
        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );

        $stmt->setFetchMode( PDO::FETCH_ASSOC );

        $stmt->execute();

        return $stmt->fetchAll();
    }


   
    

    public static function getUserIncomesFromCategory ($user_id, $income_category_id) 
    {
        $db = static::getDB();
        
        $stmt = $db->prepare( 'SELECT * FROM incomes WHERE user_id = :user_id AND income_category_assigned_to_user_id =:income_category_id' );
        
        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':income_category_id', $income_category_id, PDO::PARAM_INT ); 
        
        $stmt->setFetchMode( PDO::FETCH_ASSOC );
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public static function getSingleCategoryIncomes ($date, $user_id, $income_category_id) 
    {
        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT * FROM incomes WHERE user_id = :user_id AND income_category_assigned_to_user_id =:income_category_id AND date_of_income BETWEEN :first_date AND :second_date ORDER BY date_of_income DESC' );
        
        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':first_date', $date['first_date'], PDO::PARAM_STR );
        $stmt->bindValue( ':second_date', $date['second_date'], PDO::PARAM_STR );
        $stmt->bindValue( ':income_category_id', $income_category_id, PDO::PARAM_INT ); 
        
        $stmt->setFetchMode( PDO::FETCH_ASSOC );
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public static function getSingleIncomeData ($income_category_id) 
    {
        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT * FROM incomes WHERE id = :id');

        $stmt->bindValue( ':id', $income_category_id, PDO::PARAM_INT );

        $stmt->setFetchMode( PDO::FETCH_ASSOC );
        $stmt->execute();

        return $stmt->fetchAll();
    }
    */
}