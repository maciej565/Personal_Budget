<?php

namespace App\Models;

use PDO;
use \App\Token;

//class remembered login
// PHP version 7.4

class RememberedLogin extends \Core\Model
{

    //find remembered login token
    public static function findByToken($token)
    {
        $token = new Token($token);
        $token_hash = $token->getHash();

        $sql = 'SELECT * FROM remembered_logins
                WHERE token_hash = :token_hash';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $token_hash, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    //get  user for association
    public function getUser()
    {
        return User::findByID($this->user_id);
    }

    //token expired or not
    public function hasExpired()
    {
        return strtotime($this->expires_at) < time();
    }

    //delete activated login 
    public function delete()
    {
        $sql = 'DELETE FROM remembered_logins
                WHERE token_hash = :token_hash';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $this->token_hash, PDO::PARAM_STR);

        $stmt->execute();
    }
}