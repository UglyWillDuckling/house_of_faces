<?php

    use Tricky\helpers\Request\Request;
    use Tricky\helpers\QueryObject\QueryObject;


    $strap->setPost('profile/changeProfilePicture', 'profile.changeProfilePicture', function() use($strap){

        $picture_id = Request::getPost('id');

        if($picture_id){

            try{
            
                $db = $strap->db;
                $json = $strap->Json;

                $db->setTable('pictures');

                $q = new QueryObject;

                $q->setRule(['id', '=', $picture_id, 'AND']);
                $q->setRule(['user_id', '=', Request::getSession('user_id')]);

                $image = $db->where($q)->prvi();

                if($image){

                    //now update the user_image
                    $db->setTable('users');

                    $q->clearRules();

                    $db->set([
                        'user_image' => $picture_id
                    ], [
                        ['id', '=', Request::getSession('user_id')]
                    ]);

                    $json->setValue('path', $image['path']);
                }
            }
            catch(PDOException $e){

                $strap->logDbError($e);
                echo $e->getMessage();

                $json->setMessage(['error', 'database error.']);
            }
            finally{
                $json->send();
            }
        }
    }, ['member']);