<?php

namespace App;

//Token
//PHP version 7.4

class Token
{

    //Token value
    protected $token;
   //Constructor
    public function __construct($token_value = null)
    {
        if ($token_value) 
        {
            $this->token = $token_value;
        } else 
        {
            // 16 bytes = 128 bits = 32 hex characters
            $this->token = bin2hex(random_bytes(16));  
        }
    }
    //get token value
    public function getValue()
    {
        return $this->token;
    }
    //Get hashed Token
    public function getHash()
    {
        // sha256 = 64 chars
        return hash_hmac('sha256', $this->token, \App\Config::SECRET_KEY);  
    }
}