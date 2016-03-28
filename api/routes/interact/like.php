<?php
    use Tricky\helpers\Request\Request;
    use Tricky\helpers\QueryObject\QueryObject;

    $strap->setPost('like', 'like', function() use($strap){

        $db = $strap->db;
        $json = $strap->Json;

       $content_id = Request::getPost('content_id');

       //ako je content_id zadan i broj je izvrsavanje se nastavlja
        if( $content_id && is_numeric($content_id) )
        {   
            $db->setTable('likes');      
            $q = new QueryObject;

            $userId = Request::getSession('user_id');

        //provjeri postoji li ovaj like u tablici
            $q->setRule(['content_id', '=', $content_id, 'AND'], 'id');
            $q->setRule(['user_id', '=', $userId], 'userId');


            try{  
                $like = $db->where($q)->getAll();

             //ako like vec ne postoji dodajemo ga
                if(!$like)
                {                   
                    $db->add([
                        'user_id'    => $userId,
                        'content_id' => $content_id
                    ]);
                }
             //u slučaju da like postoji brisemo ga iz tablice 
                else{                         
                   $db->delete($q);
                } 

                $json->setValue('ok', 'true');
                $json->send();
          }
          catch(PDOExcepton $e){
                $json->setMessage(['error' => 'pogreška u bazi podataka.']);
                $json->send();

                $strap->logDbError($e);
          } 
        }
        else{
            $strap->logUserError(new Exception('content_id not supplied with like.'));
        }
    }, ['member']);