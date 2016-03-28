<?php
    function sendActivationEmail($email, $name, $code){

        $transport = \Swift_SmtpTransport::newInstance(
            'smtp.gmail.com',
            465,
            'ssl'
        )
        ->setUsername('vladokiller33@gmail.com')
        ->setPassword('obiyouwan1');

    //message    
        $message = \Swift_Message::newInstance('activaton Email')  
                 ->setFrom(array('vladokiller33@gmail.com' => 'vladimir'))
            ->setTo(array($email, $email => $name));

        $imgPath = INC_FOLDER . "/public/activation_email/friends.jpg";
        $cid = $message->embed(Swift_Image::fromPath($imgPath));//we save the path to the image so we can use it in the email 

    //message body    
        ob_start();
        require INC_FOLDER . "/public/activation_email/email.php";

        $emailBody = ob_get_clean();//we get the body of the email from an php file   

        $message->setBody($emailBody, 'text/html');


    //sending the email    
        $mailer = \Swift_Mailer::newInstance($transport);

        $s = $mailer->send($message);

        if(!$s){
            throw new Exception('unable to send the confirmation email.');
        }
    }