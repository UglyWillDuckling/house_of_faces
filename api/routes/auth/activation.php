<?php
    use Tricky\helpers\Request\Request;
    use Tricky\helpers\QueryObject\QueryObject;


    $strap->setGet('auth/activation', 'auth/activation', function() use($strap){
       
        $email = Request::getGet('email');
        //check if the email exists in the db and it is not activated
        if($email){

            $db = $strap->db;
    
            $db->setTable('users');
    
            $q = new QueryObject;
    
            $q->setRule(['email', '=', $email, 'AND']);
            $q->setRule(['activated', '=', '0']);
    
            $db->where($q);
    
            if($member = $db->prvi()){
    
                preCode($member);
                require INC_FOLDER . "/api/functions/sendActivationEmail.php";
    
                sendActivationEmail($email, $member['name'], $member['activation_code']);
            }
    
            $strap->render('activation/activation.twig', [
                'email' => $email,
            ]);
        }else{
            $strap->redirect('home');
        }
    });