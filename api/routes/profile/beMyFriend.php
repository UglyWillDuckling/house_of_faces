<?php
    use Tricky\helpers\Request\Request; 
    use Tricky\helpers\QueryObject\QueryObject; 


    $strap->setPost('profile/beMyFriend', 'profile.beMyFriend', function() use($strap){

        $user_id = Request::getPost('user_id');

        if( $user_id && $user_id != Request::getSession('user_id') )//check to make sure the user doesnt send a request to himself
        {
            try{
                $json = $strap->Json;
                $db = $strap->db;
                $member = $strap->member;

               
                $q = new QueryObject;

                $db->setTable('friend_requests');

                $q->setRule(['user_id', '=', $user_id, 'AND'], 'user_id');
                $q->setRule(['friend_id', '=', $member->id], 'friend_id');
                $friend = $db->where($q)->prvi();

                $q->setRule(['user_id', '=', $member->id, 'AND'], 'user_id');
                $q->setRule(['friend_id', '=', $user_id], 'friend_id');
                $user = $db->where($q)->prvi();


                if($friend || $user) { return 0; } //if there is any kind of relationship between the two users we terminate the script


            //if the check goes well we insert the new request    
                $db->add([
                    'user_id' => $member->id,
                    'friend_id' => $user_id,
                    'accepted' => 0,           
                ]);

                $json->setValue('ok', true);
            }
            catch(PDOException $e){
                echo $e->getMessage();

                $strap->logDbError($e);
                $json->setMessage(['error' => 'error with the database.']);
            }
            catch(Exception $e){
                echo $e->getMessage();

                $strap->logUserError($e);
                $json->setMessage(['error' => 'error with the database.']);
            }
            finally{
                $json->send();
            }   
        }
    }, ['member']);