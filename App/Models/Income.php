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

    public static function getSingleIncomeData ($incomeId) 
    {
        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT * FROM incomes WHERE id = :id');

        $stmt->bindValue( ':id', $incomeId, PDO::PARAM_INT );

        $stmt->setFetchMode( PDO::FETCH_ASSOC );
        $stmt->execute();

        return $stmt->fetchAll();
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

    public static function checkIncomeCategoryRecordsExists($user_id, $deletedIncomesCategoryName) 
    {
        
        $deletedIncomesCategoryId = static::getEditedIncomeCategoryId($user_id, $deletedIncomesCategoryName);
        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT * FROM incomes WHERE user_id = :user_id AND income_category_assigned_to_user_id =:category_id' );

        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':category_id', $deletedIncomesCategoryId, PDO::PARAM_STR );
        $stmt->setFetchMode( PDO::FETCH_ASSOC );
        $stmt->execute();

        return $stmt->fetchAll();
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



    public static function addNewIncomeCategory( $user_id, $newIncomeCategoryName)
    {
    	$db = static::getDB();
        
        $stmt = $db->prepare('INSERT INTO incomes_category_assigned_to_users VALUES (NULL, :user_id, :name)');
        
        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $newIncomeCategoryName, PDO::PARAM_STR );        
        
        return $stmt->execute();

    }

    public static function getUserIncomesCategoryId( $user_id, $incomesCategoryName)
    {

        $db = static::getDB();
        
        $stmt = $db->prepare( 'SELECT id FROM incomes_category_assigned_to_users WHERE name =:name AND user_id =:user_id ' );

        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $incomesCategoryName, PDO::PARAM_STR );
        
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public static function editIncomeCategory( $user_id, $oldIncomeCategoryName, $newIncomeCategoryName )
    {
    	$categoryId = static::getUserIncomesCategoryId($user_id, $oldIncomeCategoryName);
        
        $db = static::getDB();
        
        $stmt = $db->prepare( 'UPDATE incomes_category_assigned_to_users SET name = :name WHERE id = :id ' );

        $stmt->bindValue( ':id', $categoryId, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $newIncomeCategoryName, PDO::PARAM_STR );        
        
    	return $stmt->execute();	
    }

    
    public static function deleteIncomesCategory($user_id, $deletedIncomesCategoryName) 
    {
                    
        $db = static::getDB();
        
        $stmt = $db->prepare('DELETE FROM incomes_category_assigned_to_users WHERE user_id = :user_id AND name =:name');
        
        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $deletedIncomesCategoryName, PDO::PARAM_STR );        
        
        return $stmt->execute();
    }

    public static function deleteIncomesFromUserIncomeCategory($user_id, $deletedIncomesCategoryName) 
    {
       
        $deletedIncomesCategoryId = static::getUserIncomesCategoryId($user_id, $deletedIncomesCategoryName); 
        
        $db = static::getDB();
        
        $stmt = $db->prepare('DELETE FROM incomes WHERE user_id = :user_id AND income_category_assigned_to_user_id =:deletedIncomesCategoryName');
        
        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':deletedIncomesCategoryName', $deletedIncomesCategoryName, PDO::PARAM_INT );  
        
        return $stmt->execute();
    }

    public static function getUserIncomesFromCategory ($user_id, $deletedIncomesCategoryId) 
    {
        $db = static::getDB();
        
        $stmt = $db->prepare( 'SELECT * FROM incomes WHERE user_id = :user_id AND income_category_assigned_to_user_id =:deletedIncomesCategoryId' );
        
        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':deletedIncomesCategoryId', $deletedIncomesCategoryId, PDO::PARAM_INT ); 
        
        $stmt->setFetchMode( PDO::FETCH_ASSOC );
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public static function moveIncomesToDifferentCategory($user_id, $deletedIncomesCategoryName, $targetedIncomesCategoryName ) 
    {
        
        $deletedIncomesCategoryId = static::getUserIncomesCategoryId($user_id, $deletedIncomesCategoryName);
        $targetedIncomesCategoryId = static::getUserIncomesCategoryId($user_id, $targetedIncomesCategoryName);
        $userIncomes = static::getUserIncomesFromCategory($user_id, $deletedIncomesCategoryId);
                
        $db = static::getDB();
        
        foreach ($userIncomes as $income)
        {
            
            $stmt = $db->prepare('INSERT INTO incomes VALUES(NULL, :user_id, :income_category_assigned_to_user_id, :amount, :date_of_income, :income_comment)');
            
            $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
            $stmt->bindValue( ':income_category_assigned_to_user_id', $targetedIncomesCategoryId, PDO::PARAM_INT );            
            $stmt->bindValue( ':amount', $income['amount'], PDO::PARAM_STR );
            $stmt->bindValue( ':date_of_income', $income['date_of_income'], PDO::PARAM_STR );
            $stmt->bindValue( ':income_comment', $income['income_comment'], PDO::PARAM_STR );
            
            $stmt->execute();
        }
        return true;
    }

    public static function editSingleIncome($user_id, $income_id, $income_comment, $income_amount, $date_of_income, $income_category)
    {
        $db = static::getDB();

        $income_amount = str_replace( [','], ['.'], $income_amount );
        $income_category_id = static::getUserIncomeCategoryId($income_category, $user_id) ;
        
        $stmt = $db->prepare( 'UPDATE incomes SET amount = :income_amount, date_of_income =:date_of_income, income_comment = :income_comment, income_category_assigned_to_user_id = :income_category_id WHERE id = :income_id AND user_id = :user_id ');

        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':income_id', $income_id, PDO::PARAM_INT );
        $stmt->bindValue( ':income_category_id', $income_category_id, PDO::PARAM_INT );
        $stmt->bindValue( ':date_of_income', $date_of_income, PDO::PARAM_STR );
        $stmt->bindValue( ':income_amount', $income_amount, PDO::PARAM_STR );
        $stmt->bindValue( ':income_comment', $income_comment, PDO::PARAM_STR); 

        return $stmt->execute();
    }

    public static function getUserIncomeCategoryId($income_category, $user_id) 
    {
        $sql = 'SELECT id, user_id, name FROM incomes_category_assigned_to_users WHERE user_id = :user_id AND name = :name';

        $db = static::getDB();

        $stmt = $db->prepare($sql);
        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':name', $income_category, PDO::PARAM_STR );
        $stmt->execute();

        $income_categories = $stmt -> fetch();

        return $income_categories['id'];
    }

    public static function deleteSingleIncome ($user_id, $income_id) 
    {
        $db = static::getDB(); 
        
        $stmt = $db->prepare('DELETE FROM incomes WHERE user_id = :user_id AND id = :income_id ' );

        $stmt->bindValue( ':user_id', $user_id, PDO::PARAM_INT );
        $stmt->bindValue( ':income_id', $income_id, PDO::PARAM_INT );

        return $stmt->execute();
    }
}