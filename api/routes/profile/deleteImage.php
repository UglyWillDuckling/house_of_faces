<?php
    use Tricky\helpers\QueryObject\QueryObject;
    use Tricky\helpers\Request\Request;
    

        $strap->setPost('profile/deleteImage', 'profile.deleteImage', function() use($strap)
        {
            $picture_id = Request::getPost('id');

            if($picture_id){

                try{
  
                    $json = $strap->Json;
                    $db = $strap->db;

                    $q = new QueryObject;

                    $db->setTable('pictures');

                    $user_id = Request::getSession('user_id');


                //checking the ownership of the image    
                    $q->setRule(['id', '=', $picture_id]);
                    //$q->setRule(['user_id', '=', $user_id]);


                    $db->where($q);

                    if( $pic = $db->prvi() ){

                        $db->startTransaction();
                        $q->clearRules();

                        //delete the image(comments, likes, content etc.)                     
                        $content_id = $pic['content_id'];

                        /**
                         * deleteContent() deletes everything related to the given content_id
                         * and the content itself                      /(comments, replies, likes)
                         */
                        require INC_FOLDER . "/api/functions/deleteContent.php";

                        deleteContent($content_id, $db);

                        $db->rollback();
                    }
                }catch(PDOException $e){
                    echo $e->getMessage();
                    $json->setMessage(['error' => 'error with the database.']);

                    $db->rollback();

                }
                catch(Exception $e){
                    echo $e->getMessage();
                    $json->setMessage(['error' => $e->getMessage() ]);

                    $db->rollback();
                }
                finally{

                    $json->send();
                }

            }
        }, ['member']);