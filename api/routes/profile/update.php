<?php

    use Tricky\helpers\Request\Request;
    use Tricky\helpers\QueryObject\QueryObject;
    use Tricky\Member\Member;

    $strap->setPost('profile/update', 'profile.update', function() use($strap){

        $field = Request::getPost('table');
        $value = Request::getPost('value');

        if($field && $value){
          try{
            $db = $strap->db;
            $json = $strap->Json;

            $db->setTable('users');

            $db->set([
                $field => $value
            ], [
                ['id', '=', Request::getSession('user_id')]
            ]);

            $json->setValue('ok', true);
          }
          catch(PDOException $e){

            die($e->getMessage());
            $strap->logDbError($e);
            $json->setMessage(['error' => 'error with the database.']);         
          }
          catch(Exception $e){

            $strap->logUserError($e);
            $json->setMessage(['error' => 'error occurred']);
          }
          finally{
            $json->send();
          }
          
        }
    }, ['member']);
