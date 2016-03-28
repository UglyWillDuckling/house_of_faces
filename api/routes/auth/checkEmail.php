<?php
    
    use Tricky\helpers\Request\Request;
    use Tricky\helpers\QueryObject\QueryObject;

    $strap->setPost('auth/checkEmail', 'auth.checkEmail', function() use($strap){

        $email = Request::getPost('address');

       if($email){

            $db = $strap->db;
            $msg = array();

            $val = $strap->validate;
            $val->validate(['free' => [$email, 'unique(users, email)'] ]);


            if($val->passes()){
                //korisnicko ime ne postoji
                $msg['ok'] = true;
                echo "email dostupan.";
            } else{
                 $msg['ok'] = false;
            }

            echo json_encode($msg);
        }
        else {
            //log error
        }
    });