<?php

namespace App\Models;

use PDO;
use \Core\View;
use \App\Flash;



class balance extends \Core\Model
{
	public function __construct( $data = [] ) 
	{
        foreach ( $data as $key => $value ) 
        {
            $this->$key = $value;
        }
    }

  
    public static function getCurrentMonthDate()
    {
    	$first_date=date("Y-m-01");
		$second_date=date("Y-m-t");
		$date =['first_date'=>$first_date,
			'second_date'=>$second_date
		];
		return $date;
    }

     public static function getLastMonthDate()
    {
    	$y=date("Y");
		$m=date("m");

		if ($m == 1)
		{
			$y1=$y-1;
			$m1='12';
			$d1='01';
			$y2=$y-1;
			$m2='12';
			$d2='31';
		}
		else
		{	
			$y1=$y;
			$m1=$m-1;
			if($m1<10) $m1 = '0'.$m1;
			$d1='01';
			if ($m == 3)
			{
				if ((($y % 4 == 0) && ($y % 100 != 0)) || ($y % 400 == 0))
						{
							$y2 = $y;
							$m2='02';
							$d2='29';					
						}
						else
						{
							$y2 = $y;
							$m2='02';
							$d2='28';
						}
			}
			else
			{
				if($m==2||$m==4||$m==6||$m==8||$m==9||$m==11)
				{
						$y2 =$y;
						$m2=$m-1;
						if($m2<10) $m2 ='0'.$m2;
						$d2='31';					
				}
				else
				{
					$y2 = $y;
					$m2=$m-1;
					if($m2<10) $m2 = '0'.$m2;
					$d2='30';
				}
			}
		}
		$first_date="$y1"."-"."$m1"."-"."$d1";
		$second_date="$y2"."-"."$m2"."-"."$d2";

		$date =
		[
			'first_date'=>$first_date,
			'second_date'=>$second_date
		];
		return $date;
    }

    public static function getCurrentYearDate()
    {
    	$first_date=date("Y-01-01");
		$second_date=date("Y-12-31");
        
		$date=
		[
			'first_date'=>$first_date,
			'second_date'=>$second_date
		];
		return $date;
    }

    public static function getUserSelectedDate()
    {
		$date = ['first_date' => $_POST['first_date'],
            'second_date' => $_POST['second_date']];

		return $date;
    }

    public static function selectPeriod()
    {
    	$date = ['first_date' => $_POST['first_date'],
            'second_date' => $_POST['second_date']];

		return $date;  
    }

    public static function getIncomes( $date, $id) 
    {
        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT inc.amount, inc.date_of_income, inc.income_category_assigned_to_user_id, inc.income_comment, cat.name FROM incomes as inc, incomes_category_assigned_to_users AS cat WHERE inc.date_of_income BETWEEN :first_date AND :second_date AND inc.user_id = :user_id AND inc.income_category_assigned_to_user_id = cat.id ORDER BY inc.date_of_income ASC' );

        $stmt->bindValue( ':first_date', $date['first_date'], PDO::PARAM_STR ); 
        $stmt->bindValue( ':second_date', $date['second_date'], PDO::PARAM_STR );
        $stmt->bindValue( ':user_id', $id, PDO::PARAM_INT );

        $stmt->setFetchMode( PDO::FETCH_ASSOC );

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function getIncomesSum( $date, $id)
    {
        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT ROUND(SUM(inc.amount), 2) as incomeSumArray , cat.name as income_name  FROM incomes as inc, incomes_category_assigned_to_users as cat WHERE inc.user_id = :user_id AND inc.user_id = cat.user_id AND cat.id = inc.income_category_assigned_to_user_id AND inc.date_of_income BETWEEN :first_date AND :second_date GROUP BY cat.name'); 

        $stmt->bindValue( ':first_date', $date['first_date'], PDO::PARAM_STR );
        $stmt->bindValue( ':second_date', $date['second_date'], PDO::PARAM_STR );
        $stmt->bindValue( ':user_id', $id, PDO::PARAM_INT );
        $stmt->execute();
        $incomeSumArray=$stmt->fetchAll();
        $incomeSum=array_sum(array_column($incomeSumArray, 'incomeSumArray'));

        return $incomeSum;
    }

    public static function percentageIncome($date, $id)
    {

        $incomeSum = static::getIncomesSum($date, $id);     
        $balance = static::getBalance($date, $id);  
        if($balance<0)
        {
            $percentageIncome=round(($incomeSum/$balance)*(-100),2);                                                                
        }
        else if($balance>0)
        {
            $percentageIncome=round(($incomeSum/$balance)*100,2);                                                                   
        }
        else
        {
            $percentageIncome=0;
        }
        return $percentageIncome;
    } 

    public static function getExpenses( $date, $id) 
    {
        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT exp.amount, exp.date_of_expense, exp.expense_category_assigned_to_user_id, exp.expense_comment, cat.name FROM expenses AS exp, expenses_category_assigned_to_users AS cat WHERE exp.date_of_expense BETWEEN :first_date AND :second_date AND exp.user_id = :user_id AND exp.expense_category_assigned_to_user_id = cat.id ORDER BY exp.date_of_expense ASC' );

        $stmt->bindValue( ':first_date', $date['first_date'], PDO::PARAM_STR );
        $stmt->bindValue( ':second_date', $date['second_date'], PDO::PARAM_STR );
        $stmt->bindValue( ':user_id', $id, PDO::PARAM_INT );

        $stmt->setFetchMode( PDO::FETCH_ASSOC );

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function getExpensesSum( $date, $id ) 
    {
        $db = static::getDB();

        $stmt = $db->prepare( 'SELECT ROUND(SUM(exp.amount), 2) as expenseSumArray, cat.name as expense_name FROM expenses as exp, expenses_category_assigned_to_users as cat WHERE exp.user_id = :user_id AND exp.user_id = cat.user_id AND cat.id = exp.expense_category_assigned_to_user_id AND exp.date_of_expense BETWEEN :first_date AND :second_date GROUP BY cat.name'); 

        $stmt->bindValue( ':first_date', $date['first_date'], PDO::PARAM_STR );
        $stmt->bindValue( ':second_date', $date['second_date'], PDO::PARAM_STR );
        $stmt->bindValue( ':user_id', $id, PDO::PARAM_INT );
        $stmt->execute();
        $expenseSumArray=$stmt->fetchAll();
        $expenseSum=array_sum(array_column($expenseSumArray, 'expenseSumArray'));

        return $expenseSum;
    }

    public static function percentageExpense($date, $id)
    {
        $expenseSum = static::getExpensesSum($date, $id);
        $balance = static::getBalance($date, $id);

        if($balance<0)
        {
            $percentageExpense=round(($expenseSum/$balance)*(-100),2);                                                              
        }       
        else if($balance>0)
        {
            $percentageExpense=round(($expenseSum/$balance)*100,2);                                                                 
        }
        else
        {
            $percentageExpense=0;
        }
        return $percentageExpense;
    }
   
    public static function getBalance($date, $id)
    {
    	$incomeSum = static::getIncomesSum($date, $id);
    	$expenseSum = static::getExpensesSum($date, $id);    	
    	$balanceSum = $incomeSum - $expenseSum;
    	return $balanceSum;
    }
}