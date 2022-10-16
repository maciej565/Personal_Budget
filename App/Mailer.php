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
                        
        $mail->SMTPAuth   = true; 
        $mail->SMTPSecure = 'ssl';  
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 465; 
        $mail->isHTML(true);
        $mail->Username   = 'maciej.klosinski.programista@gmail.com';
        $mail->Password   = Config::PASS;                              
        
        $mail->SetFrom('maciej.klosinski.programista@gmail.com', 'Budżet Osobisty');
        $mail->addAddress($to);
       

        $mail->Subject = 'Aktywacja konta';
        $mail->Body    = $html;
        $mail->AltBody = $text;
        $mail->CharSet  = 'UTF-8';

        $mail->Send();
        
    }
}