<?php

namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\Config;

class Mailer
{
    public static function send($to, $subject, $html, $text)
    {
        $mail = new PHPMailer(true);                     
        $mail->isSMTP();                                            
        $mail->Host       = 'smtp.gmail.com';                     
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = 'budget.maciej.klosinski@gmail.com';

        $mail->Password   = Config::PASS;                               
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            
        $mail->Port       = 465;                                    

    
        $mail->setFrom('budget.maciej.klosinski@gmail.com', 'Budżet Osobisty');
        $mail->addAddress($to);
        $mail->isHTML(true); 

        $mail->Subject = 'Aktywacja konta';
        $mail->Body    = $html;
        $mail->AltBody = $text;
        $mail->CharSet  = 'UTF-8';

        $mail->send();
    }
}