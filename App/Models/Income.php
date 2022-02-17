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
}


/*



    public $faults = [];
	public $income_category = [];
	
	public function __construct($data = [])
    {
        foreach ($data as $key => $value) 
        {
            $this->$key = $value;
        };
    }

    public static function getAll()
	{
		if($user = Auth::getUser())
		{
			$user_id = $user->id;
			$db = static::getDB();
			$stmt=$db->prepare("SELECT id, name FROM incomes_category_assigned_to_users WHERE user_id=:$user_id");
          	
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
		}
	}
	
	public static function getAllUserIncomes()
	{
		if($user = Auth::getUser())
		{
			$user_id = $user->id;
			if (isset($_SESSION['date_first']) && isset($_SESSION['date_second']) )
			{
				$date_first = $_SESSION['date_first'];
				$second_date =$_SESSION['date_second'] ;
			}
			else
			{
				$date_first = '';
				$date_second = '';
			}
		
			$db = static::getDB();
			
           	$income_query = $db->prepare('SELECT inc.date_of_income, inc.amount, inc.income_comment, cat.name FROM incomes as inc INNER JOIN incomes_category_assigned_to_users as cat WHERE inc.user_id = :user_id AND inc.user_id = cat.user_id AND cat.id = inc.income_category_assigned_to_user_id AND inc.date_of_income BETWEEN :date_first AND :date_second ORDER BY inc.date_of_income DESC');
		$income_query->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
		$income_query->bindValue(':date_first', $date_first, PDO::PARAM_STR);
		$income_query->bindValue(':date_second', $date_second, PDO::PARAM_STR);
		$income_query->execute();							
		$incomes=$income_query->fetchAll();

		return $incomes;
		}
	}
	
	public function validate()
	{
									
			$this->income_value = str_replace(',','.',$this->income_value);
			
			if ($this->income_value == '')
			{
				$this->income_message[] = 'Kwota nie może być pusta';
			}
		
			if (!is_numeric($this->income_value))
			{
				$this->income_message[] = 'Kwota może zawierać jedynie cyfry';
			}
						
			$dot = '.';
			$isThere = strpos($this->income_value, $dot);
			
			if($isThere == true)
			{
				$amountPart = explode(".",$this->income_value);
				
				if(strlen($amountPart[0])>6)
				{
					$this->income_message[] = 'Maksymalna liczba cyfr przed przecinkiem wynosi 6';
				}	
				
				if(strlen($amountPart[1])>2)
				{
					$this->income_message[] = 'Maksymalna liczba cyfr po przecinku wynosi 2';
				}
			}
			if($isThere == false)
			{
				if(strlen($this->income_value)>6)
				{
					$this->income_message[] = 'Maksymalna liczba cyfr kwoty całkowitej wynosi 6';
				}
			}
			$date = $this->income_date;
			if($this->income_category == NULL)
			{
				$this->income_message[] = 'Wybierz kategorię';
			}
	}
	public function save()
    {
		$this->validate();
		
		if (empty($this->income_message)) 
		{
			if($user = Auth::getUser())
			{
				$user_id= $user->id;

				$db = static::getDB();
				$stmt = $db->query("SELECT id FROM incomes_category_assigned_to_users WHERE user_id = '$user_id' AND name ='$this->income_category'");
				$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$income_category = $results[0];
				$income_category = $income_value['id'];



				$sql = ("INSERT INTO incomes (user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment)
	VALUES (:user_id, :category_id, :value, :date, :comment)");
				
				$db = static::getDB();
				$stmt = $db->prepare($sql);

				$stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
				$stmt->bindValue(':category_id', $income_category, PDO::PARAM_STR);
				$stmt->bindValue(':value', $this->income_value, PDO::PARAM_STR);          
				$stmt->bindValue(':date', $this->income_date, PDO::PARAM_STR);
				$stmt->bindValue(':comment', $this->income_note, PDO::PARAM_STR);

				return $stmt->execute();
			}
		}
		else
		{
			return false;
		}
	}
}
*/