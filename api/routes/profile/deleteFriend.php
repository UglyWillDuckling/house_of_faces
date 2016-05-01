<?php

    use Tricky\helpers\Request\Request; 
    use Tricky\helpers\QueryObject\QueryObject; 


    $strap->setPost('profile/deleteFriend', 'profile.deleteFriend', function() use($strap){

        $user_id = Request::getPost('id');

        if($user_id){
            try{
                
               $member=$strap->member;
               $db = $strap->db;
               $json = $strap->Json;
               
                $q = new QueryObject;

                $q->setRule(['friend_id', '=', $member->id, 'AND'], 'friend_id');
                $q->setRule(['user_id', '=', $user_id], 'user_id');
                $db->delete($q);

                $q->setRule(['friend_id', '=', $user_id, 'AND'], 'friend_id');
                $q->setRule(['user_id', '=', $member->id], 'user_id');
                $db->delete($q);

                $json->setValue('ok', true); 
            }
            catch(PDOException $e){

                $strap->logDbError($e);
                echo $e->getMessage();
            }
            finally{

                $json->send();
            }
        }
    });
