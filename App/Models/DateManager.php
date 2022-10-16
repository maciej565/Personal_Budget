<?php

namespace App\Models;


use PDO;
use \Core\View;
use \App\Flash;

class DateManager extends \Core\Model
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
        $month=date("m");    
        $year=date("Y");
        $day=cal_days_in_month(CAL_GREGORIAN,$month,$year);
        $date = static::getFirstSecondDate($year,$month,$day);
        return $date;
    }
    
    public static function getFirstSecondDate($year,$month,$day)
    {      
        if($month == "year")
        {
            $first_date="$year"."-"."01-01";
            $second_date="$year"."-"."12-31";
        }
        else
        {
            $first_date="$year"."-"."$month"."-"."01";
            $second_date="$year"."-"."$month"."-".$day;
        }        
        
        $date =['first_date'=>$first_date,
                'second_date'=>$second_date ];        
        return $date;      
    }           

    public static function getLastMonthDate()
    {     
        $m=date("m");
        if($m=="01")
        {
            $month="12";
            $year = date("Y")-1;
        }
        else
        {
            $month = date($m)-1;
            $year = date("Y");
        }

        $day=cal_days_in_month(CAL_GREGORIAN,$month,$year);
        if($month<10)
        {
          $month="0".$month;
        }

        $date = static::getFirstSecondDate($year,$month,$day);
        return $date;        	
    }

    public static function getCurrentYearDate()
    {
        $year=date("y");
        $month="year";
        $day="31";
        $date = static::getFirstSecondDate($year,$month,$day);
        return $date; 
    }
    public static function getUserSelectedDate($arg1='',$arg2='')
    {
        $date = 
        [
        'first_date' => isset($_POST['first_date']) ? $_POST['first_date']: $arg1,
        'second_date' => isset($_POST['second_date']) ? $_POST['second_date']: $arg2
        ];
        return $date;
    }  
}