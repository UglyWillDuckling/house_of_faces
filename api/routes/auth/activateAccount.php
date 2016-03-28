<?php
    use Tricky\helpers\Request\Request;
    use Tricky\helpers\QueryObject\QueryObject;

    $strap->setGet('auth/activateAccount', 'auth/activateAccount', function() use($strap){

        $db = $strap->db;

        $code  = Request::getGet('code');
        $email = Request::getGet('email');

        $q = new QueryObject;
        

        $db->setTable('users');

        $q->setRule(['activation_code', '=', $code, 'AND']);
        $q->setRule(['email', '=', $email]);

        $db->where($q);

        $member = $db->prvi();
        if($member){

            if($member['activated'] == '0')//u slučaj
            {
                $db->set([
                    'activated' => '1'
                ],[
                    [
                     'email', '=', $email
                    ]         
                ]);

                $strap->redirect(
                    'home', 
                    "you're account has now been activated and you are free to login."
                );
            }
            else{
                $strap->redirect('home', "Relax, you're account has already been activated, you just need to login."
                );
            }             
        }
        else {              
            $strap->logUserError('aktivacija nepostojeceg korisnika.');
            $strap->redirect('home');
        }
    });
