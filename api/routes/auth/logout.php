<?php

    $strap->setGet('auth/logout', 'auth.logout', function() use($strap){

        $user_id = $_SESSION['user_id'];
        $_SESSION = array();//unset all session data

        $params = session_get_cookie_params();

        setCookie(  //we delete the session cookie
            session_name(),
            '',
            1,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );

        setCookie('nonce', $nonce, time() - 3600*1000, "/", "", false, true);//ponistavamo token za remember me

        try{
            $strap->db->setTable('users')
                ->set([
                    'cookie' => bin2hex(openssl_random_pseudo_bytes(128))//we set a random string so the account can't get hacked
                ],[
                    [
                       'id' , '=', $user_id
                    ]
            ]);
        }
        catch(PDOExcepton $e){
            $strap->logDbError($e);
        }
        
        session_destroy();

        $strap->redirect('home');
    }, ['member']);