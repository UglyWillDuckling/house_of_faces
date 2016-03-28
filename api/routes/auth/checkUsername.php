<?php
    use Tricky\helpers\Request\Request;
    use Tricky\helpers\QueryObject\QueryObject;

    $strap->setPost('auth/checkUsername', 'auth.checkUsername', function() use($strap){

        $username = Request::getPost('username');

        if($username){

            $db = $strap->db;
            $msg = array();

            $q = new QueryObject;

            $db->setTable('users');

            $q->setRule(['nickname', '=', $username]);

            $db->where($q);

            if(!$db->count()){
                //korisnicko ime ne postoji
                $msg['ok'] = true;
            } else{
                 $msg['ok'] = false;
            }

            echo json_encode($msg);
        }
        else {
            //log error
        }
    });