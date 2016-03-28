<?php
    use Tricky\helpers\Request\Request;
    use Tricky\helpers\QueryObject\QueryObject;


    $strap->setPost('auth/login', 'auth.login', function() use($strap){

        $db = $strap->db;
        $car = $strap->validate;

        $pass = Request::getPost('password');
        $email = Request::getPost('email');
        $rememberMe = Request::getPost('remember');

        $q = new QueryObject;

        $q->setRule(['email', '=', $email], 'email');

        try{
            $db->setTable('users');
            $db->where($q);

            $user = $db->prvi();
            $info = "";//the message for the user

          if($user){

            //check the password        
               $match = password_verify($pass, $user['password']);     
                if($match){

                    session_regenerate_id(true);//novi id za session
/*
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_level'] = $user['user_level'];
                    $_SESSION['loggedIn'] = 1;
*/

                    if($rememberMe)
                    {                       
                    //set the cookie
                        $nonce  = bin2hex(openssl_random_pseudo_bytes(128));

                        $db->setTable('users');

                        $db->set([
                            'cookie' => $nonce
                        ],[
                            [
                                'id', '=', $user['id']
                            ]
                        ]);

                        if(!$db->success){ //za slučaj da cookie funkcionalnost ne radi, samo logiramo gresku
                            $strap->logDbError(new Exception('unable to update the cookie nonce.'));
                        }

                        $life = $strap->config['cookie']['life'];


                        setCookie('nonce', $nonce, time() + 3600*1000, "/", "", false, true);//cookie je dostupan na citavoj domeni, http only je 'true'
                    }

                    $info = "you have successfully logged in.";
                }
                else{
                 $info = "The password provided doesn't match the account password.";
                }
            }
            else{
                $info = "the account with this email address doesn't exist.";            
            }

            $strap->redirect('home', $info);
        }
        catch(PDOException $e){
            echo $e->getMessage();
            $strap->logDbError($e);
        }
        
    }, ['csrf']);