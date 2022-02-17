<?php

namespace App;

use Datetime;
//Notifications
//PHP version 7.4

class Flash
{
    //Add message
    public $amountErrors = [];
    public $dateErrors = [];

    public static function validateAmount( $amount ) 
    {

        if ( empty( $amount ) ) 
        {
            $amountErrors[] = 'Wpisz kwotę!';

        }
        if ( !filter_var($amount, FILTER_VALIDATE_FLOAT)) 
        {
            $amountErrors[] = 'Niepoprawna kwota!';

        }
        if ( ( float )$amount < 0 ) 
        {
            $amountErrors[] = 'Kwota nie może być ujemna!';

        }
        if ( isset ( $amountErrors ) ) 
        {
            return $amountErrors;
        }
        return false;
    }

    public static function validateDate($date) 
    {

        $setDate = DateTime::createFromFormat( 'Y-m-d', $date);

        if (empty( $date) ) 
        {
            $dateErrors[] = 'Pole data nie może byc puste!';

        }
        if ( !$setDate ) 
        {

            $dateErrors[] = 'Poprawny format daty to dd.mm.rrrr!';

        }     

        if ( isset ( $dateErrors ) ) 
        {
            return $dateErrors;
        }
        return false;
    }

    public static function validateDateOrder ($first_date, $second_date) 
    {  
        if ($first_date <= $second_date ) 
        {
            return true;
        }
        return false;
    }

    public static function getCurrentDate() 
    {
        $current_date = new DateTime();         

        return $current_date -> format('Y-m-d');
    }
}