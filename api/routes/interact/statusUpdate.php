<?php
    use Tricky\helpers\Request\Request;
    use Tricky\helpers\QueryObject\QueryObject;
    use Tricky\Json\Json;

    $strap->setPost('statusUpdate', 'statusUpdate', function() use($strap){

        $status = Request::getPost('status');
      
        if($status)
        {

            $db = $strap->db;
            $json = $strap->Json;

            $userId = Request::getSession('user_id');

            $picturesDir = INC_FOLDER . "/public/images/temp/users/" . $userId;

            //insert the new status
            $user_id = Request::getSession('user_id');

            try{
                 $db->setTable('content');
                 $db->startTransaction();
                 $db->add([
                    'creator_id' => $user_id,
                    'type_of_content' => '1' //post ima id 1 u tablici type_of_content
                 ]);

                 $content_id = $db->lastInsertId();
                 $db->setTable('posts');

                 $db->add([
                    'tekst' => $status,
                    'owner_id' => $user_id,
                    'content_id' => $content_id,         
                 ], [
                 //drugi array ne zahtjeva 'bind' varijabli
                    'creation' => 'UTC_TIME'
                 ]);

                $id = $db->lastInsertId();
                $ok = true;

                $statPics = !is_dir_empty($picturesDir);//if the directory is not empty
                if($statPics)
                {
                    $pics = scandir($picturesDir); 
                    
                    require INC_FOLDER . "/api/functions/addImages.php";
                    if(!$images = addImages($pics, $db, $userId, $id, $strap->baseUrl))
                    {
                    $ok = false;
                    $json->setValue('error', "pogreska prilikom spremanja slika.");
                    }
                    else{
                        $json->setValue('pics', $images);
                        //delete the temporary pictures
                    }                           
                }

                if($ok)//ako je sve proslo u redu
                {
                    $msg['id'] = $id;
                    $json->setValue('id', $id);

                    $db->commit();  //potvrdujemo sve upite     
                }
                else 
                {
                    $db->rollback();                 
                }       

                $json->send();
            } 
            catch(PDOException $e){

              //poništavamo učinjene promjene u bazi podataka
                $db->rollback();

                $json->setMessage(['error', 'db error.']);
                $json->send();

                $strap->logDbError($e);
            } 
        }
        
    }, ['member', 'csrf']);

