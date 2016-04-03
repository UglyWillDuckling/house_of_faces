<?php
    use Tricky\helpers\Request\Request;
    use Tricky\helpers\QueryObject\QueryObject;


    $strap->setPost('profile/addImage', 'profile.addIamage', function() use($strap){

        $picture = Request::getFile('picture');
        $json = $strap->Json;

        if($picture){

            try{

                $db = $strap->db;
                $v  = $strap->validate;

                $tmpName = $picture['tmp_name'];

                $v->validate([
                    'image' => [ $tmpName, 'image(20)'],

                ]);

                if($v->passes()){
                    require INC_FOLDER . "/api/functions/saveImage.php";


                    $randNom = random_bytes(32);

                    $db->startTransaction();
                    $d = saveImage($randNom, $picture, Request::getSession('user_id'), $db);

                    //echo "path: " . $d['thumb_path'];
                    $json->setValue(
                        'url', 
                        $strap->baseUrl . "/public/images/thumbs/" . $d['image_name']
                    );

                     $json->setValue(
                        'id', 
                        $d['picture_id']
                    );

                    $db->commit();
                }
                else{

                    $json->setMessage(['error' => 'bad picture upload.']);
                }
            } 
            catch(PDOException $e){

                $db->rollback();
                $strap->logDbError($e);

                $json->setMessage(['error' => 'error with the database.']);
            }
            catch(Exception $e){

                $db->rollback();
                $strap->logUserError($e);

                $json->setMessage(['error' => 'error with the database.']);
            }
            finally{
                $json->send();//in the end we always send a message back to js
            }
        }
        else{
            $strap->logUserError(new Exception('no picture with the upload.'));
        }
    }, ['member']);