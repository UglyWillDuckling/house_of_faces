<?php

    use Tricky\helpers\Request\Request;
    use Tricky\helpers\QueryObject\QueryObject;
    use Tricky\Member\Member;

    $strap->setPost('profile/addContent', 'profile.addContent', function() use($strap){

        $value = Request::getPost('value');
        $table = Request::getPost('table');

        if($value && $table && strlen($value) > 3&& strlen($table) > 3){
           try{

            $db = $strap->db;
            $json = $strap->Json;

            $db->setTable($table);

            $db->add([
               'name' => $value
            ]);

            $json->setValue('id', $db->lastInsertId());

           }
           catch(PDOException $e){

              $strap->logDbError($e);
              echo $e->getMessage();  
           }
           finally{
            $json->send();
           }
        }
    }, ['member']);