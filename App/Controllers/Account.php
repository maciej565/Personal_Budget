<?php

namespace App\Controllers;

use \App\Models\User;

//Account controller
// PHP version 7.4

class Account extends \Core\Controller
{

 // Ajax valdate existed email
  public function validateEmailAction()
  {
    $is_valid = ! User::emailExists($_GET['email'], $_GET['ignore_id'] ?? null);

    header('Content-Type: application/json');
    echo json_encode($is_valid);
  }
}
