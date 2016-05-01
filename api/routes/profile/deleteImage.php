<?php
    use Tricky\helpers\QueryObject\QueryObject;
    use Tricky\helpers\Request\Request;
    

    $strap->setPost('profile/deleteImage', 'profile.deleteImage', function() use($strap)
    {
        $picture_id = Request::getPost('id');

        if($picture_id){
            try{

                $json = $strap->Json;
                $member = $strap->member;
                $db = $strap->db;


                $q = new QueryObject;

                $db->setTable('pictures');
                $user_id = Request::getSession('user_id');

            //checking the ownership of the image    
                $q->setRule(['id', '=', $picture_id]);
                $q->setRule(['user_id', '=', $user_id]);

                $db->where($q);
                if( $pic = $db->prvi() ){

                    $db->startTransaction();
                    $q->clearRules();

                    $member->deleteImage($pic['content_id']);    


                    $db->rollback();
                }
            }
            catch(PDOException $e){
                echo $e->getMessage();
                $json->setMessage(['error' => 'error with the database.']);

                $db->rollback();
            }
            catch(Exception $e)
            {
                echo $e->getMessage();
                $json->setMessage(['error' => $e->getMessage() ]);
    
                if(isset($e->tempPath)) //if the image itself was deleted we restore it
                {
                rename($e->tempPath, $e->absPath);
                }

                $db->rollback();
            }
            finally{
                $json->send();
            }
        }
    }, ['member']);
