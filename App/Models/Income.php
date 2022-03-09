<?php

namespace App\Models;

use PDO;
use \Core\View;
use \App\Flash;



class Income extends \Core\Model
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

    public function getUserCategoryId($user_id) 
    {
        $sql = 'SELECT id, user_id, name FROM incomes_category_assigned_to_users WHERE user_id = :user_id AND name = :name';

        $db = static::getDB();

        $stmt = $db->prepare($sql);
        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $this->category, PDO::PARAM_STR );
        $stmt->execute();

        $categories = $stmt -> fetch();

        return $categories['id'];
    }

    

    public function saveUserIncome($user_id) 
    {
        $this->amount = str_replace( [','], ['.'], $this->amount );
        $this->amountErrors = Flash::validateAmount( $this->amount );
        $this->dateErrors = Flash::validateDate( $this->date );

        if ( ( empty( $this->amountErrors ) ) && ( empty( $this->dateErrors ) ) ) 
        {
            $sql = 'INSERT INTO incomes VALUES(NULL, :user_id, :income_category_assigned_to_user_id, :amount, :date_of_income, :income_comment)';

            $db = static::getDB();
            $stmt = $db->prepare( $sql );
            $comment = filter_var( $this->comment, FILTER_SANITIZE_SPECIAL_CHARS);

            $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
            $stmt->bindValue( ':income_category_assigned_to_user_id', $this->getUserCategoryId($user_id), PDO::PARAM_INT );
            $stmt->bindValue( ':amount', $this->amount, PDO::PARAM_STR );
            $stmt->bindValue( ':date_of_income', $this->date, PDO::PARAM_STR );
            $stmt->bindValue( ':income_comment', $comment, PDO::PARAM_STR );

            return $stmt->execute();
        }
        return false;
    }

    public static function getIncomes( $date, $user_id ) 
    {
        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT amount, date_of_income, income_category_assigned_to_user_id, income_comment, inc.name FROM incomes, incomes_category_assigned_to_users AS inc WHERE incomes.date_of_income BETWEEN :first_date AND :second_date AND incomes.user_id = :user_id AND incomes.income_category_assigned_to_user_id = inc.id ORDER BY incomes.date_of_income ASC' );

        $stmt->bindValue( ':first_date', $date['first_date'], PDO::PARAM_STR );
        $stmt->bindValue( ':second_date', $date['second_date'], PDO::PARAM_STR );
        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );

        $stmt->setFetchMode( PDO::FETCH_ASSOC );

        $stmt->execute();

        return $stmt->fetchAll();
    }


    public static function getUserIncomeCategories( $id )
    {
        $db = static::getDB();

        $query_income_categories=$db->prepare("SELECT id, name FROM incomes_category_assigned_to_users WHERE user_id=:id");

        $query_income_categories->bindValue(':id',$_SESSION['user_id'],PDO::PARAM_INT);
        $query_income_categories->execute();
        $income_categories=$query_income_categories->fetchAll();

        return $income_categories;
    }

    public static function checkIncomeCategoryExists($user_id, $oldIncomeCategoryName) 
    {
        
        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT * FROM incomes_category_assigned_to_users WHERE user_id = :user_id AND name =:name' );

        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $oldIncomeCategoryName, PDO::PARAM_STR );
        $stmt->setFetchMode( PDO::FETCH_ASSOC );
        $stmt->execute();

        return $stmt->fetchAll();
    }


    public static function addNewIncomeCategory( $user_id, $newIncomeCategoryName)
    {
    	$db = static::getDB();
        
        $stmt = $db->prepare('INSERT INTO incomes_category_assigned_to_users VALUES (NULL, :user_id, :name)');
        
        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $newIncomeCategoryName, PDO::PARAM_STR );        
        
        return $stmt->execute();

    }

    public static function getEditedIncomeCategoryId( $user_id, $incomeCategoryName)
    {

        $db = static::getDB();
        
        $stmt = $db->prepare( 'SELECT id FROM incomes_category_assigned_to_users WHERE name =:name AND user_id =:user_id ' );

        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $incomeCategoryName, PDO::PARAM_STR );
        
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public static function editIncomeCategory( $user_id, $oldIncomeCategoryName, $newIncomeCategoryName )
    {
    	$categoryId = static::getEditedIncomeCategoryId($user_id, $oldIncomeCategoryName);
        
        $db = static::getDB();
        
        $stmt = $db->prepare( 'UPDATE incomes_category_assigned_to_users SET name = :name WHERE id = :id ' );

        $stmt->bindValue( ':id', $categoryId, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $newIncomeCategoryName, PDO::PARAM_STR );        
        
    	return $stmt->execute();	
    }

    public static function deleteIncomeCategory( $user_id, $oldIncomeCategoryName, $newIncomeCategoryName)
    {
        $categoryId = static::getEditedIncomeCategoryId($user_id, $oldIncomeCategoryName);
        
        $db = static::getDB();
        
        $stmt = $db->prepare( 'UPDATE incomes_category_assigned_to_users SET name = :name WHERE id = :id ' );

        $stmt->bindValue( ':id', $categoryId, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $newIncomeCategoryName, PDO::PARAM_STR );        
        
        return $stmt->execute();    
    }
}


