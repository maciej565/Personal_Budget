<?php

namespace App\Models;

use PDO;
use \App\Token;
use \App\Mailer;
use \Core\View;

class User extends \Core\Model
{
    public $nameErrors = [];
    public $emailErrors = [];
    public $passwordErrors = [];
    public $captchaErrors = [];

	public function __construct($data = [])
    {
        foreach ($data as $key => $value) 
        {
            $this->$key = $value;
        };
    }

    public function save()
    {
        $this->validateUserData();

        if ( ( empty( $this->nameErrors ) ) && ( empty( $this->emailErrors ) ) && ( empty( $this->passwordErrors ) )&& ( empty( $this->captchaErrors ) ) )
        {
            $password_hash = password_hash( $this->password1, PASSWORD_DEFAULT );
            $token = new Token();
            $hashed_token = $token->getHash();
            $this->activation_token = $token->getValue();
            
            $sql='INSERT INTO users (username, email, password, activation_hash)  
                             VALUES (:username, :email, :password, :activation_hash)'; 
            
            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':username', $this->username, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password', $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(':activation_hash', $hashed_token, PDO::PARAM_STR);
            return $stmt->execute();
        }
        return false;	
    }

    public function saveUserData() 
    {
        $this->saveUserIncomeCategories();
        $this->saveUserExpenseCategories();
        $this->saveUserPaymentMethods();
    }

    public function saveUserIncomeCategories() 
    {
    $sql = 'INSERT INTO incomes_category_assigned_to_users(user_id, name) 
                    SELECT (SELECT id FROM users WHERE email=:email), name 
                    FROM incomes_category_default';

        $db = static::getDB();
        $stmt = $db->prepare( $sql );

        $stmt->bindValue( ':email', $this->email, PDO::PARAM_STR );

        return $stmt->execute();
    }

    public function saveUserExpenseCategories() 
    {

        $sql = 'INSERT INTO expenses_category_assigned_to_users(user_id, name) 
                    SELECT (SELECT id FROM users WHERE email=:email), name 
                    FROM expenses_category_default';

        $db = static::getDB();
        $stmt = $db->prepare( $sql );

        $stmt->bindValue( ':email', $this->email, PDO::PARAM_STR );

        return $stmt->execute();
    }

    public function saveUserPaymentMethods() 
    {
        $sql = 'INSERT INTO payment_methods_assigned_to_users(user_id, name) 
                    SELECT (SELECT id FROM users WHERE email=:email), name 
                    FROM payment_methods_default';

        $db = static::getDB();
        $stmt = $db->prepare( $sql );

        $stmt->bindValue( ':email', $this->email, PDO::PARAM_STR );

        return $stmt->execute();
    }

    public function validateUserData() 
    {

        // name validation
        if ( $this->username == '' ) 
        {
            $this->nameErrors[] = 'Podaj login';
        }

        if ( mb_strlen( $this->username ) <= 2 ) 
        {
            $this->nameErrors[] = 'Login powinien posiadać co najmniej 2 znaki';
        }

        // email validation
        if ( filter_var( $this->email, FILTER_VALIDATE_EMAIL ) === false ) 
        {
            $this->emailErrors[] = 'Nieprawidłowy adres email!';
        }
        if ( static::emailExists( $this->email, $this->user_id ?? null ) ) 
        {
            $this->emailErrors[] = 'Adres email jest już zajęty';
        }

        // password validation
        if ( isset( $this->password1 ) ) 
        {

            if ( ( strlen( $this->password1 ) < 8 ) || ( strlen( $this->password1 ) ) > 20 ) 
            {
                $this->passwordErrors[] = 'Hasło musi posiadać od 8 do 20 znaków!';
            }

            if ( preg_match( '/.*[a-z]+.*/i', $this->password1 ) == 0 ) 
            {
                $this->passwordErrors[] = 'Hasło musi posiadać co najmniej jedną literę';
            }

            if ( preg_match( '/.*\d+.*/i', $this->password1 ) == 0 ) 
            {
                $this->passwordErrors[] = 'Hasło musi posiadać co najmniej jedną cyfrę';
            }

            if ( $this->password1 != $this->password2) 
            {
                $this->passwordErrors[] = 'Podane hasła nie są identyczne!';
            }
        }

         $secret_key = "6Lf-uuAdAAAAAP6fjZpvSjmY0AXI3B5H__4vWqKV";
        $check_secret_key = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret_key.'&response='.$_POST['g-recaptcha-response']);
        $captcha_response = json_decode($check_secret_key);

        if ($captcha_response->success==false)
        {
            $this->captchaErrors[]="Potwierdź, że nie jesteś botem!";
        }       
    }

    public static function emailExists($email, $ignore_id = null)
    {
        $user = static::findByEmail($email);
        if ($user) 
        {
            if ($user->id != $ignore_id) 
            {
                return true;
            }
        }
    }

    public static function findByEmail($email)
    {
        $sql = 'SELECT * FROM users WHERE email = :email';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetch();
    }

    
    //Authenticate email & password
    public static function authenticate($email, $password)
    {
        $user = static::findByEmail($email);
        if ($user && $user->is_active) 
        {
            if (password_verify($password, $user->password)) 
            {
                return $user;
            }
        }
        return false;
    }
    public static function findByID( $id ) 
    {
        $sql = 'SELECT * FROM users WHERE id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare( $sql );
        $stmt->bindValue( ':user_id', $id, PDO::PARAM_INT );

        $stmt->setFetchMode( PDO::FETCH_CLASS, get_called_class() );

        $stmt->execute();

        return $stmt->fetch();
    }
    public static function getUsername( $id ) 
    {
        $sql = 'SELECT username FROM users WHERE id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare( $sql );
        $stmt->bindValue( ':user_id', $id, PDO::PARAM_INT );

        $stmt->execute();
        $name=$stmt->fetch();
       

        return $name['username'];
    }


    
    public function rememberLogin()
    {
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->remember_token = $token->getValue();

        //$expiry_timestamp  - 30 days in seconds
        $this->expiry_timestamp = time() + 60 * 60 * 24 * 30;
        $sql = 'INSERT INTO remembered_logins(user_id, token_hash, expires_at)
                     VALUES (:user_id, :token_hash, :expires_at)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiry_timestamp), PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function sendActivationEmail()
    {
      $url = 'https://' . $_SERVER['HTTP_HOST'] . '/signup/activate/' . $this->activation_token;
        $text = View::getTemplate('Signup/activation_email.txt', ['url' => $url]);
        $html = View::getTemplate('Signup/activation_email.html', ['url' => $url]); 
        Mailer::send($this->email, 'Aktywacja konta', $html, $text);   
    }

    public static function findByToken($hashed_token)
    {
        $sql = 'SELECT * FROM users WHERE activation_hash = :hashed_token';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':hashed_token', $hashed_token, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function activate($value)
    {
        $token = new Token($value);
        $hashed_token = $token->getHash();
        $user = static::findByToken($hashed_token);

        $sql = 'UPDATE users
                SET is_active = 1,
                    activation_hash = null
                WHERE activation_hash = :hashed_token';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':hashed_token', $hashed_token, PDO::PARAM_STR);
        $stmt->execute();
    }
}